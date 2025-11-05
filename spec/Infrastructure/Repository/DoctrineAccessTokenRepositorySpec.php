<?php

namespace spec\Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use PhpSpec\ObjectBehavior;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Trailmind\Access\AccessToken;
use Trailmind\Access\Exception\AccessTokenNotFoundException;
use Trailmind\User\User;

class DoctrineAccessTokenRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(AccessToken::class)->willReturn($repository);
        $this->beConstructedWith($entityManager);
    }
    
    function it_should_return_an_AccessToken_by_id(
        EntityRepository $repository,
        AccessToken $accessToken
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn($accessToken);
        
        $this->findOneById('12345')->shouldReturn($accessToken);
    }
    
    function it_should_throw_an_AccessTokenNotFoundException_if_id_not_found(
        EntityRepository $repository,
        AccessToken $accessToken
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn(null);
        
        $this->shouldThrow(AccessTokenNotFoundException::class)->duringFindOneById('12345');
    }
    
    function it_should_throw_an_AccessTokenNotFoundException_if_id_is_malformed(
        EntityRepository $repository,
        AccessToken $accessToken
    ) {
        $repository->findOneBy(['id' => 'Please let me in'])->willThrow(ConversionException::class);
        
        $this->shouldThrow(AccessTokenNotFoundException::class)->duringFindOneById('Please let me in');
    }
    
    function it_should_return_all_AccessTokens(
        EntityRepository $repository,
        AccessToken $accessToken1,
        AccessToken $accessToken2,
        AccessToken $accessToken3
    ) {
        $repository->findAll()->willReturn([$accessToken1, $accessToken2, $accessToken3]);
        
        $this->findAll()->shouldReturn([$accessToken1, $accessToken2, $accessToken3]);
    }
    
    function it_should_find_an_unrevoked_AccessToken_by_user(
        EntityRepository $repository,
        AccessToken $accessToken,
        User $user
    ) {
        $repository->findOneBy(['user' => $user, 'revoked'=> false])->willReturn($accessToken);
        $this->findUnrevokedByUser($user)->shouldReturn($accessToken);
    }
    
    function it_should_throw_an_AccessTokenNotFoundException_if_no_unrevoked_AccessToken_by_user_id_found(
        EntityRepository $repository,
        AccessToken $accessToken,
        User $user
    ) {
        $repository->findOneBy(['user' => $user, 'revoked'=> false])->willReturn(null);
        $this->shouldThrow(AccessTokenNotFoundException::class)->duringFindUnrevokedByUser($user);
    }
    
    function it_should_be_able_to_persist_an_AccessToken(
        EntityManagerInterface $entityManager,
        AccessToken $accessToken
    ) {
        $entityManager->persist($accessToken)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();
        
        $this->save($accessToken);
    }
}
