<?php

namespace spec\Infrastructure\Oauth2Server\Bridge;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Trailmind\Access\AccessTokenRepository;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Infrastructure\Oauth2Server\Bridge\AccessToken;
use Infrastructure\Oauth2Server\Bridge\Client;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Trailmind\Access\AccessToken as TrailmindAccessToken;
use Trailmind\Access\Exception\AccessTokenNotFoundException;
use Trailmind\User\User;
use Trailmind\User\UserRepository;
use Trailmind\Access\Client as TrailmindClient;
use Trailmind\Access\ClientRepository;

class OauthAccessTokenRepositorySpec extends ObjectBehavior
{
    function let(
        AccessTokenRepository $accessTokenRepository,
        UserRepository $userRepository,
        ClientRepository $clientRepository
    ) {
        $this->beConstructedWith($accessTokenRepository, $userRepository, $clientRepository);
    }

    function it_should_be_an_AccessTokenRepositoryInterface()
    {
        $this->shouldBeAnInstanceOf(AccessTokenRepositoryInterface::class);
    }

    function it_should_return_a_new_AccessToken(
        ClientEntityInterface $clientEntity,
        ScopeEntityInterface $scope1,
        ScopeEntityInterface $scope2
    ) {
        $scope1->getIdentifier()->willReturn("1");
        $scope2->getIdentifier()->willReturn("2");

        $token = $this->getNewToken($clientEntity, [$scope1, $scope2], "id");

        $token->shouldBeAnInstanceOf(AccessToken::class);
        $token->getUserIdentifier()->shouldReturn("id");
        $token->getScopes()->shouldReturn([$scope1, $scope2]);
    }

    function it_should_persist_an_access_token(
        AccessTokenEntityInterface $accessTokenEntity,
        AccessTokenRepository $accessTokenRepository,
        Client $client,
        ScopeEntityInterface $scope1,
        ScopeEntityInterface $scope2,
        UserRepository $userRepository,
        User $user,
        ClientRepository $clientRepository,
        TrailmindClient $ffclient
    ) {
        $scope1->getIdentifier()->willReturn("1");
        $scope2->getIdentifier()->willReturn("2");
        $client->getIdentifier()->willReturn("3");

        $accessTokenEntity->getIdentifier()->willReturn("1");
        $accessTokenEntity->getUserIdentifier()->willReturn("22");
        $accessTokenEntity->getClient()->willReturn($client);
        $accessTokenEntity->getScopes()->willReturn([$scope1, $scope2]);
        $accessTokenEntity->getExpiryDateTime()->willReturn(new \DateTimeImmutable());

        $userRepository->findOneById("22")->willReturn($user);
        $clientRepository->findActiveById("3")->willReturn($ffclient);

        $accessTokenRepository->save(Argument::type(TrailmindAccessToken::class))->shouldBeCalled();

        $this->persistNewAccessToken($accessTokenEntity);
    }

    function it_should_be_able_to_revoke_an_access_token(
        AccessTokenRepository $accessTokenRepository,
        TrailmindAccessToken $accessToken
    ) {
        $accessTokenRepository->findOneById("1234")->willReturn($accessToken);
        $accessToken->revoke()->shouldBeCalled();
        $accessTokenRepository->save($accessToken)->shouldBeCalled();

        $this->revokeAccessToken("1234");
    }

    function it_should_not_be_able_to_revoke_an_access_token_that_cannot_be_found(
        AccessTokenRepository $accessTokenRepository
    ) {
        $accessTokenRepository->findOneById("1234")->willThrow(AccessTokenNotFoundException::class);
        $accessTokenRepository->save(Argument::type(TrailmindAccessToken::class))->shouldNotBeCalled();

        $this->revokeAccessToken("1234");
    }

    function it_should_be_able_to_tell_if_an_access_token_is_revoked(
        AccessTokenRepository $accessTokenRepository,
        TrailmindAccessToken $accessToken
    ) {
        $accessTokenRepository->findOneById("1234")->willReturn($accessToken);
        $accessToken->isRevoked()->willReturn(false);

        $this->isAccessTokenRevoked("1234")->shouldReturn(false);
    }

    function it_should_tell_an_access_token_is_revoked_if_no_token_can_be_found(
        AccessTokenRepository $accessTokenRepository,
        TrailmindAccessToken $accessToken
    ) {
        $accessTokenRepository->findOneById("1234")->willThrow(AccessTokenNotFoundException::class);

        $this->isAccessTokenRevoked("1234")->shouldReturn(true);
    }
}
