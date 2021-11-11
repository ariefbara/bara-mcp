<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotSetting;

class ChangeMentoringSlotSettingPayload
{

    /**
     * 
     * @var string|null
     */
    protected $mentoringSlotId;

    /**
     * 
     * @var MentoringSlotSetting|null
     */
    protected $mentoringSlotSetting;

    public function getMentoringSlotId(): ?string
    {
        return $this->mentoringSlotId;
    }

    public function getMentoringSlotSetting(): ?MentoringSlotSetting
    {
        return $this->mentoringSlotSetting;
    }

    public function __construct(?string $mentoringSlotId, ?MentoringSlotSetting $mentoringSlotSetting)
    {
        $this->mentoringSlotId = $mentoringSlotId;
        $this->mentoringSlotSetting = $mentoringSlotSetting;
    }

}
