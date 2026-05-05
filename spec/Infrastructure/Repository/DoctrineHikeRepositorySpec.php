<?php

namespace spec\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Trailmind\Hike\Hike;
use Trailmind\User\User;

class DoctrineHikeRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(Hike::class)->willReturn($repository);
        $this->beConstructedWith($entityManager);
    }

    function it_should_return_a_Hike_by_id(
        EntityRepository $repository,
        Hike $hike
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn($hike);

        $this->findOneById('12345')->shouldReturn($hike);
    }

    function it_should_save_a_Hike(
        EntityManagerInterface $entityManager,
        Hike $hike
    ) {
        $entityManager->persist($hike)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->save($hike);
    }
    
    function it_should_return_all_Hikes_by_user(
        EntityManagerInterface $entityManager,
        Hike $hike1,
        Hike $hike2,
        User $user,
        QueryBuilder $queryBuilder,
        Query $query
    ) {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($queryBuilder);
        
        $queryBuilder->select('h')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->from(Hike::class, 'h')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->where('h.user = :user')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('user', $user)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getResult()->shouldBeCalled()->willReturn([$hike1, $hike2]);

        $this->findAllByUser($user)->shouldReturn([$hike1, $hike2]);
    }
}