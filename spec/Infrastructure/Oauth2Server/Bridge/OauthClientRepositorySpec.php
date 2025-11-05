<?php

namespace spec\Infrastructure\Oauth2Server\Bridge;

use PhpSpec\ObjectBehavior;
use Trailmind\Access\Client as TrailmindClient;
use Trailmind\Access\ClientRepository;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Infrastructure\Oauth2Server\Bridge\Client;

class OauthClientRepositorySpec extends ObjectBehavior
{
    function let(ClientRepository $clientRepository)
    {
        $this->beConstructedWith($clientRepository);
    }

    function it_should_be_an_ClientRepositoryInterface()
    {
        $this->shouldBeAnInstanceOf(ClientRepositoryInterface::class);
    }

    function it_should_return_a_new_ClientEntity(
        ClientRepository $clientRepository,
        TrailmindClient $client
    ) {
        $client->getSecret()->willReturn("abcd");
        $client->getName()->willReturn("bob");
        $client->getRedirect()->willReturn("/");

        $clientRepository->findActiveById("1234")->willReturn($client);

        $oAuthclient = $this->getClientEntity("1234", "password", "abcd");

        $oAuthclient->shouldBeAnInstanceOf(Client::class);
    }

    function it_should_not_return_a_new_ClientEntity_if_invalid_secret(
        ClientRepository $clientRepository,
        TrailmindClient $client
    ) {
        $client->getSecret()->willReturn("abcd");
        $client->getName()->willReturn("bob");
        $client->getRedirect()->willReturn("/");

        $clientRepository->findActiveById("1234")->willReturn($client);

        $oAuthclient = $this->getClientEntity("1234", "password", "zyxw", true);

        $oAuthclient->shouldBe(null);
    }
}
