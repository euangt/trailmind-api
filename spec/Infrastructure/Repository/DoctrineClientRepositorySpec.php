<?php

namespace spec\Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use PhpSpec\ObjectBehavior;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Trailmind\Access\Client;
use Trailmind\Access\Exception\ClientNotFoundException;

class DoctrineClientRepositorySpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $entityManager,
        EntityRepository $repository
    ) {
        $entityManager->getRepository(Client::class)->willReturn($repository);
        $this->beConstructedWith($entityManager);
    }

    function it_should_return_an_active_Client_by_id(
        EntityRepository $repository,
        Client $client
    ) {
        $repository->findOneBy(['id' => '12345', 'active'=>true])->willReturn($client);

        $this->findActiveById('12345')->shouldReturn($client);
    }

    function it_should_throw_a_ClientNotFoundException_if_id_not_found(
        EntityRepository $repository,
    ) {
        $repository->findOneBy(['id' => '12345', 'active'=>true])->willReturn(null);

        $this->shouldThrow(ClientNotFoundException::class)->duringFindActiveById('12345');
    }

    function it_should_throw_a_ClientNotFoundException_if_id_is_malformed(
        EntityRepository $repository,
    ) {
        $repository->findOneBy(['id' => 'My Pc', 'active'=>true])->willThrow(ConversionException::class);

        $this->shouldThrow(ClientNotFoundException::class)->duringFindActiveById('My Pc');
    }

    function it_should_return_a_client_by_id_and_secret(
        EntityRepository $repository,
        Client $client
    ) {
        $repository->findOneBy(['id' => '12345', 'secret'=>'abcde'])->willReturn($client);

        $this->findOneByIdAndSecret('12345', 'abcde')->shouldReturn($client);
    }

    function it_should_throw_a_clientNotFoundException_if_id_and_secret_does_not_match(
        EntityRepository $repository,
    ) {
        $repository->findOneBy(['id' => '12345', 'secret'=>'abcde'])->willReturn(null);

        $this->shouldThrow(ClientNotFoundException::class)->duringFindOneByIdAndSecret('12345', 'abcde');
    }

    function it_should_be_able_to_persist_a_Client(
        EntityManagerInterface $entityManager,
        Client $client
    ) {
        $entityManager->persist($client)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->save($client);
    }
}
