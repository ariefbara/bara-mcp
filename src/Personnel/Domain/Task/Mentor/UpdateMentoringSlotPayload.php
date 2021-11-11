<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;

class UpdateMentoringSlotPayload
{

    /**
     * 
     * @var string|null
     */
    protected $id;

    /**
     * 
     * @var MentoringSlotData|null
     */
    protected $mentoringSlotData;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMentoringSlotData(): ?MentoringSlotData
    {
        return $this->mentoringSlotData;
    }

    public function __construct(?string $id, ?MentoringSlotData $mentoringSlotData)
    {
        $this->id = $id;
        $this->mentoringSlotData = $mentoringSlotData;
    }

}
