<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequestData;

class RequestMentoringPayload {

    /**
     * 
     * @var MentoringRequestData
     */
    protected $mentoringRequestData;
    protected $consultationSetupId;
    protected $participantId;
    public $requestedMentoringId;

    public function getMentoringRequestData(): MentoringRequestData {
        return $this->mentoringRequestData;
    }

    public function getConsultationSetupId() {
        return $this->consultationSetupId;
    }

    public function getParticipantId() {
        return $this->participantId;
    }

    public function __construct(MentoringRequestData $mentoringRequestData, $consultationSetupId, $participantId) {
        $this->mentoringRequestData = $mentoringRequestData;
        $this->consultationSetupId = $consultationSetupId;
        $this->participantId = $participantId;
    }

}
