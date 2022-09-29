<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\User\UserParticipantRepository;

class ViewUserParticipantDetail implements ProgramTaskExecutableByCoordinator, ProgramTaskExecutableByConsultant
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->userParticipantRepository->aUserParticipantInProgram($programId, $payload->getId());
    }

}
