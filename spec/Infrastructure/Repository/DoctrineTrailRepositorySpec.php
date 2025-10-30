<?php

namespace spec\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class DoctrineTrailRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(Trail::class)->willReturn($repository);
        $this->beConstructedWith($entityManager);
    }

    function it_should_return_a_Trail_by_id(
        EntityRepository $repository,
        Trail $trail
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn($trail);

        $this->findOneById('12345')->shouldReturn($trail);
    }

    function it_should_save_a_Trail(
        EntityManagerInterface $entityManager,
        Trail $trail
    ) {
        $entityManager->persist($trail)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->save($trail);
    }
}