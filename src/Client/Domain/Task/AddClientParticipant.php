<?php

namespace Client\Domain\Task;

use Client\Domain\Model\Client;
use Client\Domain\Model\IClientTask;
use Client\Domain\Task\Repository\Firm\Client\ClientParticipantRepository;
use Client\Domain\Task\Repository\Firm\Program\ParticipantRepository;

class AddClientParticipant implements IClientTask
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;
    public $addedClientParticipantId;

    public function __construct(
            ClientParticipantRepository $clientParticipantRepository, ParticipantRepository $participantRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param Client $client
     * @param string $payload participantId
     * @return void
     */
    public function execute(Client $client, $payload): void
    {
        $participant = $this->participantRepository->ofId($payload);
        $clientParticipant = $client->createClientParticipant($payload, $participant);
        $this->clientParticipantRepository->add($clientParticipant);
        $this->addedClientParticipantId = $payload;
    }

}
