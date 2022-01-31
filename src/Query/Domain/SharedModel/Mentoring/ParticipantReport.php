<?php

namespace Query\Domain\SharedModel\Mentoring;

use Query\Domain\Model\Shared\ContainFormRecordInterface;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\SharedModel\Mentoring;

class ParticipantReport implements ContainFormRecordInterface
{

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var int|null
     */
    protected $mentorRating;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    public function getId(): string
    {
        return $this->id;
    }

    public function getMentorRating(): ?int
    {
        return $this->mentorRating;
    }

    protected function __construct()
    {
        
    }

    public function getSubmitTimeString(): ?string
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
