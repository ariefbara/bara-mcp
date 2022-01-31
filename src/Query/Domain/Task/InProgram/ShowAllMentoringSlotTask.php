<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;

class ShowAllMentoringSlotTask implements ITaskInProgramExecutableByParticipant
{
    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;
    
    /**
     * 
     * @var ShowAllMentoringSlotPayload
     */
    protected $payload;
    
    /**
     * 
     * @var MentoringSlot[]
     */
    public $results;
    
    public function __construct(MentoringSlotRepository $mentoringSlotRepository, ShowAllMentoringSlotPayload $payload)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->payload = $payload;
    }

    
    public function executeTaskInProgram(string $programId): void
    {
        $this->results = $this->mentoringSlotRepository->allMentoringSlotInProgram(
                $programId, $this->payload->getPage(), $this->payload->getPageSize(), $this->payload->getFilter());
    }

}
