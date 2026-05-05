<?php

namespace spec\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Trailmind\Access\AccessToken;
use Trailmind\User\Exception\UserNotFoundException;
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

    function it_should_return_a_User_by_email(
        EntityRepository $repository,
        User $user
    ) {
        $repository->findOneBy(['email' => 'user@example.com'])->willReturn($user);

        $this->findOneByEmail('user@example.com')->shouldReturn($user);
    }

    function it_should_throw_if_user_id_is_not_found(
        EntityRepository $repository
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn(null);

        $this->shouldThrow(UserNotFoundException::class)->duringFindOneById('12345');
    }

    function it_should_throw_if_user_email_is_not_found(
        EntityRepository $repository
    ) {
        $repository->findOneBy(['email' => 'user@example.com'])->willReturn(null);

        $this->shouldThrow(UserNotFoundException::class)->duringFindOneByEmail('user@example.com');
    }

    function it_should_return_a_user_by_access_token(
        EntityManagerInterface $entityManager,
        QueryBuilder $queryBuilder,
        Query $query,
        User $user
    ) {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->select('u')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->from(User::class, 'u')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->from(AccessToken::class, 'a')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->where('a.user = u')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('a.id = :accessToken')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('a.revoked = false')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('a.expiresAt > :now')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('accessToken', 'access-token-id')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('now', Argument::type(\DateTimeImmutable::class))->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled()->willReturn($user);

        $this->findOneByAccessToken('access-token-id')->shouldReturn($user);
    }

    function it_should_throw_if_access_token_user_is_not_found(
        EntityManagerInterface $entityManager,
        QueryBuilder $queryBuilder,
        Query $query
    ) {
        $entityManager->createQueryBuilder()->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->select('u')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->from(User::class, 'u')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->from(AccessToken::class, 'a')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->where('a.user = u')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('a.id = :accessToken')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('a.revoked = false')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('a.expiresAt > :now')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('accessToken', 'missing-access-token')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('now', Argument::type(\DateTimeImmutable::class))->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->getQuery()->shouldBeCalled()->willReturn($query);
        $query->getOneOrNullResult()->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(UserNotFoundException::class)->duringFindOneByAccessToken('missing-access-token');
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
