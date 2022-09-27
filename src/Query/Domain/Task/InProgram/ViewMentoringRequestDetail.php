<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class ViewMentoringRequestDetail implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

    public function __construct(MentoringRequestRepository $mentoringRequestRepository)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->mentoringRequestRepository->aMentoringRequestInProgram($programId, $payload->getId());
    }

}
