<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Consultant\MentoringSlot;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;

class ViewAllMentoringSlotsTask implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var ViewAllMentoringSlotPayload
     */
    protected $payload;

    /**
     * 
     * @var MentoringSlot[]|null
     */
    public $result;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository, ViewAllMentoringSlotPayload $payload)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): void
    {
        $this->result = $this->mentoringSlotRepository->allMentoringSlotsBelongsToPersonnel(
                $personnelId, $this->payload->getPage(), $this->payload->getPageSize(), $this->payload->getFilter());
    }

}
