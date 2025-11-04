<?php

namespace spec\Dto\Outbound\Authentication;

use Dto\Internal\Authentication\AccessToken;
use Dto\Outbound\EntityBuilder;
use Dto\Outbound\EntityDto;
use PhpSpec\ObjectBehavior;

class AccessTokenBuilderSpec extends ObjectBehavior
{
    function it_should_be_an_entity_builder()
    {
        $this->shouldBeAnInstanceOf(EntityBuilder::class);
    }

    function it_should_initialise_an_access_token(
        AccessToken $accessToken,
    ) {
        $accessToken = new AccessToken(
            'access_token_value',
            'refresh_token_value',
            3600
        );

        $this->build($accessToken)->shouldReturnAnInstanceOf(EntityDto::class);
    }

    function it_should_not_allow_any_other_initialisable()
    {
        $this->shouldThrow(\UnexpectedValueException::class)->duringBuild('not an access token');
    }

    function it_should_allow_a_context_to_be_set()
    {
        $this->setContext('context')->shouldReturn($this);
    }
}