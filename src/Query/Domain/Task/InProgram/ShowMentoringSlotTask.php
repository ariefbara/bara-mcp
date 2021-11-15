<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;

class ShowMentoringSlotTask implements ITaskInProgramExecutableByParticipant
{

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var MentoringSlot
     */
    public $result;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository, string $id)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->id = $id;
    }

    public function executeTaskInProgram(string $programId): void
    {
        $this->result = $this->mentoringSlotRepository->aMentoringSlotInProgram($programId, $this->id);
    }

}
