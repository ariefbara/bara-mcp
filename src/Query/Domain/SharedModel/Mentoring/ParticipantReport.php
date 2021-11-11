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

    public function getUnremovedAttachmentFieldRecords(): array
    {
        return $this->formRecord->getUnremovedAttachmentFieldRecords();
    }

    public function getUnremovedIntegerFieldRecords(): array
    {
        return $this->formRecord->getUnremovedIntegerFieldRecords();
    }

    public function getUnremovedMultiSelectFieldRecords(): array
    {
        return $this->formRecord->getUnremovedMultiSelectFieldRecords();
    }

    public function getUnremovedSingleSelectFieldRecords(): array
    {
        return $this->formRecord->getUnremovedSingleSelectFieldRecords();
    }

    public function getUnremovedStringFieldRecords(): array
    {
        return $this->formRecord->getUnremovedStringFieldRecords();
    }

    public function getUnremovedTextAreaFieldRecords(): array
    {
        return $this->formRecord->getUnremovedTextAreaFieldRecords();
    }

}
