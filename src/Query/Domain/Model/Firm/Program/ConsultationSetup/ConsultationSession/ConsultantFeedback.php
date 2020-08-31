<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

use Query\Domain\Model\ {
    Firm\Program\ConsultationSetup\ConsultationSession,
    Shared\ContainFormRecordInterface,
    Shared\FormRecord
};

class ConsultantFeedback implements ContainFormRecordInterface
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

    function getConsultationSession(): ConsultationSession
    {
        return $this->consultationSession;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getSubmitTimeString(): string
    {
        return $this->formRecord->getSubmitTimeString();
    }

    public function getUnremovedAttachmentFieldRecords()
    {
        return $this->formRecord->getUnremovedAttachmentFieldRecords();
    }

    public function getUnremovedIntegerFieldRecords()
    {
        return $this->formRecord->getUnremovedIntegerFieldRecords();
    }

    public function getUnremovedMultiSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedMultiSelectFieldRecords();
    }

    public function getUnremovedSingleSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedSingleSelectFieldRecords();
    }

    public function getUnremovedStringFieldRecords()
    {
        return $this->formRecord->getUnremovedStringFieldRecords();
    }

    public function getUnremovedTextAreaFieldRecords()
    {
        return $this->formRecord->getUnremovedTextAreaFieldRecords();
    }

}
