<?php

namespace spec\Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use PhpSpec\ObjectBehavior;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Trailmind\Access\RefreshToken;
use Trailmind\Access\AccessToken;
use Trailmind\Access\Exception\RefreshTokenNotFoundException;

class DoctrineRefreshTokenRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(RefreshToken::class)->willReturn($repository);
        $this->beConstructedWith($entityManager);
    }
    
    function it_should_return_a_RefreshToken_by_id(
        EntityRepository $repository,
        RefreshToken $refreshToken
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn($refreshToken);
        
        $this->findOneById('12345')->shouldReturn($refreshToken);
    }
    
    function it_should_throw_a_RefreshTokenNotFoundException_if_id_not_found(
        EntityRepository $repository,
    ) {
        $repository->findOneBy(['id' => '12345'])->willReturn(null);
        
        $this->shouldThrow(RefreshTokenNotFoundException::class)->duringFindOneById('12345');
    }

    function it_should_throw_a_RefreshTokenNotFoundException_if_id_is_malformed(
        EntityRepository $repository,
    ) {
        $repository->findOneBy(['id' => 'Let Me In'])->willThrow(ConversionException::class);
        
        $this->shouldThrow(RefreshTokenNotFoundException::class)->duringFindOneById('Let Me In');
    }
    
    function it_should_return_all_RefreshTokens(
        EntityRepository $repository,
        RefreshToken $refreshToken1,
        RefreshToken $refreshToken2,
        RefreshToken $refreshToken3
    ) {
        $repository->findAll()->willReturn([$refreshToken1, $refreshToken2, $refreshToken3]);
        
        $this->findAll()->shouldReturn([$refreshToken1, $refreshToken2, $refreshToken3]);
    }
    
    function it_should_find_a_RefreshToken_by_AccessToken(
        EntityRepository $repository,
        AccessToken $accessToken,
        RefreshToken $refreshToken
    ) {
        $repository->findOneBy(['accessToken' => $accessToken])->willReturn($refreshToken);
        $this->findOneByAccessToken($accessToken)->shouldReturn($refreshToken);
    }
    
    function it_should_throw_a_RefreshTokenNotFoundException_if_no_AccessToken_found(
        EntityRepository $repository,
        AccessToken $accessToken,
    ) {
        $repository->findOneBy(['accessToken' => $accessToken])->willReturn(null);
        $this->shouldThrow(RefreshTokenNotFoundException::class)->duringFindOneByAccessToken($accessToken);
    }
    
    function it_should_be_able_to_persist_a_RefreshToken(
        EntityManagerInterface $entityManager,
        RefreshToken $refreshToken
    ) {
        $entityManager->persist($refreshToken)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();
        
        $this->save($refreshToken);
    }
}
