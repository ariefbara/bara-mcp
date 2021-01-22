<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\BioForm;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Shared\ContainFormRecordInterface;
use Query\Domain\Model\Shared\FormRecord;

class ClientBio implements ContainFormRecordInterface
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var BioForm
     */
    protected $bioForm;

    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;

    /**
     * 
     * @var bool
     */
    protected $removed;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getBioForm(): BioForm
    {
        return $this->bioForm;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        
    }
    
    public function getId(): string
    {
        return $this->formRecord->getId();
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
