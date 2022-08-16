<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Task\Dependency\Firm\Client\ClientParticipantRepository;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Resources\Application\Event\AdvanceDispatcher;

class AcceptProgramApplicationFromClient implements FirmTask
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

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(
            ClientParticipantRepository $clientParticipantRepository, ClientRepository $clientRepository,
            ProgramRepository $programRepository, AdvanceDispatcher $dispatcher)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->clientRepository = $clientRepository;
        $this->programRepository = $programRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * 
     * @param Firm $firm
     * @param AcceptProgramApplicationFromClientPayload $payload
     * @return void
     */
    public function execute(Firm $firm, $payload): void
    {
        $payload->acceptedClientParticipantId = $this->clientParticipantRepository->nextIdentity();

        $client = $this->clientRepository->ofId($payload->getClientId());
        $client->assertUsableInFirm($firm);
        $program = $this->programRepository->aProgramOfId($payload->getProgramId());
        $program->assertUsableInFirm($firm);

        $clientParticipant = $client->addAsProgramApplicant($payload->acceptedClientParticipantId, $program);
        $this->clientParticipantRepository->add($clientParticipant);
        
        $this->dispatcher->dispatch($clientParticipant);
    }

}
