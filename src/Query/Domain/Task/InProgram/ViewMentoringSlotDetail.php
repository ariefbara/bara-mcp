<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;

class ViewMentoringSlotDetail implements ProgramTaskExecutableByConsultant, ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->mentoringSlotRepository->aMentoringSlotInProgram($programId, $payload->getId());
    }

}
