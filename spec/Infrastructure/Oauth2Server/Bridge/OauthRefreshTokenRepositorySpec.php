<?php

namespace spec\Infrastructure\Oauth2Server\Bridge;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Trailmind\Access\AccessTokenRepository;
use Trailmind\Access\RefreshTokenRepository;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Trailmind\Access\RefreshToken as TrailmindRefreshToken;
use Trailmind\Access\AccessToken as TrailmindAccessToken;
use Trailmind\Access\Exception\RefreshTokenNotFoundException;

class OauthRefreshTokenRepositorySpec extends ObjectBehavior
{
    function let(
        AccessTokenRepository $accessTokenRepository,
        RefreshTokenRepository $refreshTokenRepository
    ) {
        $this->beConstructedWith($accessTokenRepository, $refreshTokenRepository);
    }

    function it_should_be_a_RefreshTokenRepositoryInterface()
    {
        $this->shouldBeAnInstanceOf(RefreshTokenRepositoryInterface::class);
    }

    function it_should_return_a_new_RefreshToken()
    {
        $this->getNewRefreshToken()->shouldBeAnInstanceOf(RefreshTokenEntityInterface::class);
    }

    function it_should_persist_a_refresh_token(
        RefreshTokenEntityInterface $refreshTokenEntity,
        AccessTokenEntityInterface $accessTokenEntity,
        RefreshTokenRepository $refreshTokenRepository,
        AccessTokenRepository $accessTokenRepository,
        TrailmindAccessToken $accessToken
    ) {
        $accessTokenEntity->getIdentifier()->willReturn("a");

        $refreshTokenEntity->getIdentifier()->willReturn("1");
        $refreshTokenEntity->getAccessToken()->willReturn($accessTokenEntity);
        $refreshTokenEntity->getExpiryDateTime()->willReturn(new \DateTimeImmutable());

        $accessTokenRepository->findOneById('a')->willReturn($accessToken);

        $refreshTokenRepository->save(Argument::type(TrailmindRefreshToken::class))->shouldBeCalled();

        $this->persistNewRefreshToken($refreshTokenEntity);
    }

    function it_should_be_able_to_revoke_a_refresh_token(
        RefreshTokenRepository $refreshTokenRepository,
        TrailmindRefreshToken $refreshToken
    ) {
        $refreshTokenRepository->findOneById("1234")->willReturn($refreshToken);
        $refreshToken->revoke()->shouldBeCalled();
        $refreshTokenRepository->save($refreshToken)->shouldBeCalled();

        $this->revokeRefreshToken("1234");
    }

    function it_should_not_be_able_to_revoke_a_refresh_token_that_cannot_be_found(
        RefreshTokenRepository $refreshTokenRepository
    ) {
        $refreshTokenRepository->findOneById("1234")->willThrow(RefreshTokenNotFoundException::class);
        $refreshTokenRepository->save(Argument::type(TrailmindRefreshToken::class))->shouldNotBeCalled();

        $this->revokeRefreshToken("1234");
    }

    function it_should_be_able_to_tell_if_a_refresh_token_is_revoked(
        RefreshTokenRepository $refreshTokenRepository,
        TrailmindRefreshToken $refreshToken
    ) {
        $refreshTokenRepository->findOneById("1234")->willReturn($refreshToken);
        $refreshToken->isRevoked()->willReturn(true);

        $this->isrefreshTokenRevoked("1234")->shouldReturn(true);
    }

    function it_should_tell_a_refresh_token_is_revoked_if_no_token_can_be_found(
        RefreshTokenRepository $refreshTokenRepository
    ) {
        $refreshTokenRepository->findOneById("1234")->willThrow(RefreshTokenNotFoundException::class);

        $this->isRefreshTokenRevoked("1234")->shouldReturn(true);
    }

    function it_should_determine_a_refresh_token_is_revoked_if_related_access_token_is(
        RefreshTokenRepository $refreshTokenRepository,
        TrailmindRefreshToken $refreshToken,
        TrailmindAccessToken $accessToken
    ) {
        $refreshTokenRepository->findOneById("1234")->willReturn($refreshToken);
        $refreshToken->isRevoked()->willReturn(false);
        $refreshToken->getAccessToken()->willReturn($accessToken);
        $accessToken->isRevoked()->willReturn(true);

        $this->isrefreshTokenRevoked("1234")->shouldReturn(true);
    }
}
