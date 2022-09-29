<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Client\ClientParticipantRepository;

class ViewClientParticipantDetail implements ProgramTaskExecutableByCoordinator, ProgramTaskExecutableByConsultant
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->clientParticipantRepository->aClientParticipantInProgram($programId, $payload->getId());
    }

}
