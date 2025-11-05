<?php

namespace spec\Trailmind\Access;

use PhpSpec\ObjectBehavior;
use DateTime;
use DateTimeImmutable;
use Trailmind\User\User;
use Trailmind\Access\Client;

class AccessTokenSpec extends ObjectBehavior
{
    const ID = '54321';

    function let(
        User $user,
        Client $client,
        DateTime $created,
        DateTime $updated,
        DateTimeImmutable $expires
    ) {
        $this->beConstructedWith(
            self::ID,
            $user,
            $client,
            ['*'],
            $created,
            $updated,
            $expires
        );
    }

    function it_should_be_created_with_an_id()
    {
        $this->getId()->shouldReturn(self::ID);
    }

    function it_should_be_created_with_a_user(User $user)
    {
        $this->getUser()->shouldReturn($user);
    }

    function it_should_be_created_with_a_client(Client $client)
    {
        $this->getClient()->shouldReturn($client);
    }

    function it_should_be_constructed_with_scopes()
    {
        $this->getScopes()->shouldReturn(['*']);
    }

    function it_should_be_constructed_as_unrevoked()
    {
        $this->isRevoked()->shouldReturn(false);
    }

    function it_should_be_revokable()
    {
        $this->revoke();
        $this->isRevoked()->shouldReturn(true);
    }

    function it_should_be_constructed_with_a_CreatedAt_date(
        DateTime $created
    ) {
        $this->getCreatedAt()->shouldReturn($created);
    }

    function it_should_be_constructed_with_an_UpdatedAt_date(
        DateTime $updated
    ) {
        $this->getUpdatedAt()->shouldReturn($updated);
    }

    function it_should_be_constructed_with_an_ExpiresAt_date(
        DateTimeImmutable $expires
    ) {
        $this->getExpiresAt()->shouldReturn($expires);
    }

    function it_should_be_updatable(
        DateTime $lastUpdate
    ) {
        $this->setUpdatedAt($lastUpdate);

        $this->getUpdatedAt()->shouldReturn($lastUpdate);
    }
}
