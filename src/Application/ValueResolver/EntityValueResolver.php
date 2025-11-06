<?php

namespace Application\ValueResolver;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsTargetedValueResolver('entity')]
class EntityValueResolver extends CoreValueResolver implements ValueResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $options = $this->getOptions($argument);

        $repository = $this->determineRepository($options);
        [$requestMapping, $repositoryMapping] = $this->getMappingKeys($options);
        $value = $this->getMappingValue($request, $requestMapping);

        if (is_null($value) || is_null($repositoryMapping) || is_null($requestMapping)) {
            if ($this->isNullable($options)) {
                return [];
            } else {
                throw new BadRequestHttpException(sprintf('Unable to process this request without a value for %s', $requestMapping));
            }
            return [];
        }

        $entity = $repository->findOneBy([
            $repositoryMapping => $value,
        ]);

        if (is_null($entity)) {
            if ($this->isNullable($options)) {
                // if this is nullable, return an empty array
                return [];
            } else {
                // otherwise throw letting the user know what key was missing
                throw new NotFoundHttpException(sprintf('Could not find entity with %s %s', $repositoryMapping, $value));
            }
        }

        return [$entity];
    }

    public function determineRepository(array $options)
    {
        $class = $options['class'];

        return $this->entityManager->getRepository($class);
    }

    /**
     * Returns an array with:
     *  the key required to extract the entity ID from the request, and
     *  the key required to look the entity up in the database
     *  e.g.
     *  ['entity_id', 'id']
     *
     * @return array
     */
    private function getMappingKeys(array $options)
    {
        $requestMapping = null;
        $repositoryMapping = null;
        if (array_key_exists('mapping', $options)) {
            $mapping = $options['mapping'];
            // mapping array looks like ['requestMapping' => 'responseMapping']
            $requestMapping = array_keys($mapping)[0];
            $repositoryMapping = $mapping[$requestMapping];
        } else if (array_key_exists('key', $options)) {
            $key = $options['key'];
            // key array looks like 'key' => ['requestMapping' => 'responseMapping']
            $requestMapping = array_keys($key)[0];
            $repositoryMapping = $key[$requestMapping];
        }

        return [$requestMapping, $repositoryMapping];
    }

    /**
     * @throws BadRequestHttpException
     *
     * @return array
     */
    private function getMappingValue(Request $request, string $requestMapping)
    {
        $value = $request->get($requestMapping);

        if (is_null($value)) {
            $data = json_decode($request->getContent(), true);
            $value = $data[$requestMapping] ?? null;
        }
        return $value;
    }
}