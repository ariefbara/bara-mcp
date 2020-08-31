<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\Model\Participant\ConsultationSession;
use SharedContext\Domain\Model\SharedEntity\{
    FormRecord,
    FormRecordData
};

class ParticipantFeedback
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
