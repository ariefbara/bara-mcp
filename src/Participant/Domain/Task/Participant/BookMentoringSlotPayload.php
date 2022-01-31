<?php

namespace Participant\Domain\Task\Participant;

class BookMentoringSlotPayload
{

    /**
     * 
     * @var string|null
     */
    protected $mentoringSlotId;

    public function getMentoringSlotId(): ?string
    {
        return $this->mentoringSlotId;
    }

    public function __construct(?string $mentoringSlotId)
    {
        $this->mentoringSlotId = $mentoringSlotId;
    }

}
