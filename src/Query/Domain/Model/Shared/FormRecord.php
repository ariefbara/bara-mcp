<?php

namespace Query\Domain\Model\Shared;

use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};

class FormRecord
{

    /**
     *
     * @var Form
     */
    protected $form;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

    /**
     * 
     * @var ArrayCollection
     */
    protected $integerFieldRecords;

    /**
     * 
     * @var ArrayCollection
     */
    protected $stringFieldRecords;

    /**
     * 
     * @var ArrayCollection
     */
    protected $textAreaFieldRecords;

    /**
     * 
     * @var ArrayCollection
     */
    protected $singleSelectFieldRecords;

    /**
     * 
     * @var ArrayCollection
     */
    protected $multiSelectFieldRecords;

    /**
     * 
     * @var ArrayCollection
     */
    protected $attachmentFieldRecords;

    function getForm(): Form
    {
        return $this->form;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getSubmitTimeString(): ?string
    {
        return isset($this->submitTime)? $this->submitTime->format("Y-m-d H:i:s"): null;
    }

    function getUnremovedIntegerFieldRecords()
    {
        return $this->integerFieldRecords->matching($this->unremovedCriteria())->getIterator();
    }

    function getUnremovedStringFieldRecords()
    {
        return $this->stringFieldRecords->matching($this->unremovedCriteria())->getIterator();
    }

    function getUnremovedTextAreaFieldRecords()
    {
        return $this->textAreaFieldRecords->matching($this->unremovedCriteria())->getIterator();
    }

    function getUnremovedSingleSelectFieldRecords()
    {
        return $this->singleSelectFieldRecords->matching($this->unremovedCriteria())->getIterator();
    }

    function getUnremovedMultiSelectFieldRecords()
    {
        return $this->multiSelectFieldRecords->matching($this->unremovedCriteria())->getIterator();
    }

    function getUnremovedAttachmentFieldRecords()
    {
        return $this->attachmentFieldRecords->matching($this->unremovedCriteria())->getIterator();
    }

    protected function __construct()
    {
        ;
    }

    protected function unremovedCriteria()
    {
        return Criteria::create()
                        ->andWhere(Criteria::expr()->eq('removed', false));
    }

}
