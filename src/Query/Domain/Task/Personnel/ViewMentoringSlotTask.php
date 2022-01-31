<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;

class ViewMentoringSlotTask implements TaskExecutableByPersonnel
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
    protected $mentoringSlotId;

    /**
     * 
     * @var MentoringSlot|null
     */
    public $result = null;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository, string $mentoringSlotId)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->mentoringSlotId = $mentoringSlotId;
    }

    public function execute(string $personnelId): void
    {
        $this->result = $this->mentoringSlotRepository
                ->aMentoringSlotBelongsToPersonnel($personnelId, $this->mentoringSlotId);
    }

}
