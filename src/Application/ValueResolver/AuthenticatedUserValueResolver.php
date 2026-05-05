<?php

namespace Application\ValueResolver;

use Infrastructure\Security\CurrentUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;

#[AsTargetedValueResolver('authenticated_user')]
class AuthenticatedUserValueResolver extends CoreValueResolver implements ValueResolverInterface
{
    public function __construct(
        private CurrentUserProvider $currentUserProvider,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $options = $this->getOptions($argument);

        try {
            $user = $this->currentUserProvider->findUser();
        } catch (InvalidAccessTokenException $iate) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid authentication token');
        }

        if ($user === null) {
            if ($this->isNullable($options)) {
                return [];
            }

            throw new UnauthorizedHttpException('Bearer', 'Authentication token required');
        }

        return [$user];
    }
}
