<?php

namespace spec\Application\ValueResolver;

use Application\ValueResolver\CustomisableValueResolver;
use Application\ValueResolver\CoreValueResolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use stdClass;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityValueResolverSpec extends ObjectBehavior
{
    function it_should_be_a_ValueResolver()
    {
        assert ($this->getWrappedObject() instanceof ValueResolverInterface);
    }

    function it_should_be_a_CoreValueResolver()
    {
        assert ($this->getWrappedObject() instanceof CoreValueResolver);
    }

    function let(
        ArgumentMetadata $argument,
        EntityManagerInterface $entityManager,
        CustomisableValueResolver $customisableValueResolver
    ) {
        $this->beConstructedWith($entityManager);

        $argument->getAttributes()->willReturn([$customisableValueResolver]);
        $customisableValueResolver->getOptions()->willReturn(['class' => stdClass::class, 'mapping' => ['requestMapping' => 'repoMapping']]);
    }

    function it_should_resolve_with_entity(
        ArgumentMetadata $argument,
        Request $request,
        stdClass $entity,
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(stdClass::class)->willReturn($repository);
        $request->get('requestMapping')->willReturn('abcd-1234');
        $repository->findOneBy(['repoMapping' => 'abcd-1234'])->willReturn($entity);

        $this->resolve($request, $argument)->shouldReturn([$entity]);
    }

    function it_should_resolve_with_nullable_entity(
        ArgumentMetadata $argument,
        Request $request,
        EntityManagerInterface $entityManager,
        EntityRepository $repository,
        CustomisableValueResolver $customisableValueResolver
    ) {
        $customisableValueResolver->getOptions()->willReturn(['class' => stdClass::class, 'mapping' => ['requestMapping' => 'repoMapping'], 'nullable' => true]);

        $entityManager->getRepository(stdClass::class)->willReturn($repository);
        $request->get('requestMapping')->willReturn('abcd-1234');
        $repository->findOneBy(['repoMapping' => 'abcd-1234'])->willReturn(null);

        $this->resolve($request, $argument)->shouldReturn([]);
    }

    function it_should_resolve_with_entity_from_request(
        ArgumentMetadata $argument,
        Request $request,
        stdClass $entity,
        EntityManagerInterface $entityManager,
        EntityRepository $repository,
        CustomisableValueResolver $customisableValueResolver
    ) {
        $customisableValueResolver->getOptions()->willReturn(['class' => stdClass::class, 'mapping' => ['requestMapping' => 'repoMapping']]);

        $entityManager->getRepository(stdClass::class)->willReturn($repository);
        $request->get('requestMapping')->willReturn('abcd-1234');
        $repository->findOneBy(['repoMapping' => 'abcd-1234'])->willReturn($entity);

        $this->resolve($request, $argument)->shouldReturn([$entity]);
    }

    function it_should_throw_if_entity_is_not_found(
        ArgumentMetadata $argument,
        Request $request,
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(stdClass::class)->willReturn($repository);
        $request->get('requestMapping')->willReturn('abcd-1234');
        $repository->findOneBy(['repoMapping' => 'abcd-1234'])->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->duringResolve($request, $argument);
    }

    function it_should_throw_a_BadRequestHttpException_if_entity_not_found_and_nullable_is_false(
        ArgumentMetadata $argument,
        Request $request,
        EntityManagerInterface $entityManager,
        EntityRepository $repository,
        CustomisableValueResolver $customisableValueResolver
    ) {
        $customisableValueResolver->getOptions()->willReturn(['class' => stdClass::class, 'mapping' => ['requestMapping' => 'repoMapping'], 'nullable' => false]);

        $entityManager->getRepository(stdClass::class)->willReturn($repository);
        $request->get('requestMapping')->willReturn('abcd-1234');
        $repository->findOneBy(['repoMapping' => 'abcd-1234'])->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->duringResolve($request, $argument);
    }
}