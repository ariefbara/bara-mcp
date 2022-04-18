<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Domain\Model\Client\ClientRegistrant;
use Client\Domain\Task\Repository\Firm\Client\ClientRegistrantRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineClientRegistrantRepository extends DoctrineEntityRepository implements ClientRegistrantRepository
{

    public function add(ClientRegistrant $clientRegistrant): void
    {
        $this->persist($clientRegistrant);
    }

}
