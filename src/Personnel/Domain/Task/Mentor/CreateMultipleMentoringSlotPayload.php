<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotSetting;

class CreateMultipleMentoringSlotPayload
{

    /**
     * 
     * @var string|null
     */
    protected $consultationSetupId;

    /**
     * 
     * @var MentoringSlotData[]
     */
    protected $mentoringSlotDataCollection;

    public function getConsultationSetupId(): ?string
    {
        return $this->consultationSetupId;
    }

    public function getMentoringSlotDataCollection(): array
    {
        return $this->mentoringSlotDataCollection;
    }

    public function __construct(?string $consultationSetupId)
    {
        $this->consultationSetupId = $consultationSetupId;
        $this->mentoringSlotDataCollection = [];
    }
    
    public function addMentoringSlotData(MentoringSlotData $mentoringSlotData): void
    {
        $this->mentoringSlotDataCollection[] = $mentoringSlotData;
    }

}
