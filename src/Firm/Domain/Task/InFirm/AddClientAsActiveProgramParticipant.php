<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\TaskInFirmExecutableByManager;
use Firm\Domain\Task\Dependency\Firm\Client\ClientParticipantRepository;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;

class AddClientAsActiveProgramParticipant implements TaskInFirmExecutableByManager
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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

    public function __construct(
            ClientParticipantRepository $clientParticipantRepository, ClientRepository $clientRepository,
            ProgramRepository $programRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * 
     * @param Firm $firm
     * @param AddClientAsActiveProgramParticipantPayload $payload
     * @return void
     */
    public function execute(Firm $firm, $payload): void
    {
        $payload->addedClientParticipantId = $this->clientParticipantRepository->nextIdentity();
        $client = $this->clientRepository->ofId($payload->getClientId());
        $program = $this->programRepository->aProgramOfId($payload->getProgramId());
        
        $client->assertUsableInFirm($firm);
        $program->assertUsableInFirm($firm);
        
        $clientParticipant = $client->addAsActiveProgramParticipant($payload->addedClientParticipantId, $program);
        $this->clientParticipantRepository->add($clientParticipant);
    }

}
