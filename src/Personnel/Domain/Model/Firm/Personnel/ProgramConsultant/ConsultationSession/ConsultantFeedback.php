<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};

class ConsultantFeedback
{

    /**
     *
     * @var ConsultationSession
     */
    protected $consultationSession;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    function __construct(ConsultationSession $consultationSession, string $id, FormRecord $formRecord)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->formRecord = $formRecord;
    }

    public function update(FormRecordData $formRecordData): void
    {
        $this->formRecord->update($formRecordData);
    }

}
