<?php

namespace spec\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Trailmind\User\User;

class DoctrineUserRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(User::class)->willReturn($repository);
        $this->beConstructedWith($entityManager);
    }

    function it_should_return_a_User_by_id(
        EntityRepository $repository,
        User $user
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn($user);

        $this->findOneById('12345')->shouldReturn($user);
    }

    function it_should_save_a_User(
        EntityManagerInterface $entityManager,
        User $user
    ) {
        $entityManager->persist($user)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->save($user);
    }
}