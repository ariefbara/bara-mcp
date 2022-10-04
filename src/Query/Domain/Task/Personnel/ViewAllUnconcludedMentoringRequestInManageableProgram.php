<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class ViewAllUnconcludedMentoringRequestInManageableProgram implements PersonnelTask
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
     * @param string $personnelId
     * @param ViewAllUnconcludedMentoringRequestInManageableProgramPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->mentoringRequestRepository
                ->allUnconcludedMentoringRequestsInProgramManageableByPersonnel($personnelId,
                $payload->getPaginationFilter());
    }

}
