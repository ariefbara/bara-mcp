<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;

class AddClientParticipantTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var AddClientParticipantPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $addedClientParticipantId;

    public function __construct(
            ClientRepository $clientRepository, ProgramRepository $programRepository,
            AddClientParticipantPayload $payload)
    {
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $program = $this->programRepository->aProgramOfId($this->payload->getProgramId());
        $program->assertUsableInFirm($firm);
        $client = $this->clientRepository->ofId($this->payload->getClientId());
        $client->assertUsableInFirm($firm);
        $this->addedClientParticipantId = $client->addIntoProgram($program);
    }

}
