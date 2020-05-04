<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\{
    Firm\Program\Mission,
    Firm\Program\Participant,
    Shared\ContainFormRecordInterface,
    Shared\FormRecord
};

class Worksheet implements ContainFormRecordInterface
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var Worksheet
     */
    protected $parent = null;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var Mission
     */
    protected $mission;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getParent(): Worksheet
    {
        return $this->parent;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getMission(): Mission
    {
        return $this->mission;
    }

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function isRemoved(): bool
    {
        return $this->removed;
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
