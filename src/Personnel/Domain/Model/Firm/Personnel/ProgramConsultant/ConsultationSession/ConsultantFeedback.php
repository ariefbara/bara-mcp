<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Shared\Domain\Model\ {
    FormRecord,
    FormRecord\AttachmentFieldRecord,
    FormRecord\IntegerFieldRecord,
    FormRecord\MultiSelectFieldRecord,
    FormRecord\SingleSelectFieldRecord,
    FormRecord\StringFieldRecord,
    FormRecord\TextAreaFieldRecord,
    FormRecordData,
    HasFormRecordInterface
};

class ConsultantFeedback implements HasFormRecordInterface
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

    function getSubmitTimeString(): string
    {
        return $this->formRecord->getSubmitTimeString();
    }

    /**
     * 
     * @return IntegerFieldRecord[]
     */
    function getUnremovedIntegerFieldRecords()
    {
        return $this->formRecord->getUnremovedIntegerFieldRecords();
    }

    /**
     * 
     * @return StringFieldRecord[]
     */
    function getUnremovedStringFieldRecords()
    {
        return $this->formRecord->getUnremovedStringFieldRecords();
    }

    /**
     * 
     * @return TextAreaFieldRecord[]
     */
    function getUnremovedTextAreaFieldRecords()
    {
        return $this->formRecord->getUnremovedTextAreaFieldRecords();
    }

    /**
     * 
     * @return SingleSelectFieldRecord[]
     */
    function getUnremovedSingleSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedSingleSelectFieldRecords();
    }

    /**
     * 
     * @return MultiSelectFieldRecord[]
     */
    function getUnremovedMultiSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedMultiSelectFieldRecords();
    }

    /**
     * 
     * @return AttachmentFieldRecord[]
     */
    function getUnremovedAttachmentFieldRecords()
    {
        return $this->formRecord->getUnremovedAttachmentFieldRecords();
    }

}
