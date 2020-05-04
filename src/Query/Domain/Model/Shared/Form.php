<?php

namespace Query\Domain\Model\Shared;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};

class Form
{

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
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var ArrayCollection
     */
    protected $stringFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $integerFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $textAreaFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $attachmentFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $singleSelectFields;

    /**
     *
     * @var ArrayCollection
     */
    protected $multiSelectFields;

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): string
    {
        return $this->description;
    }

    function getUnremovedStringFields()
    {
        return $this->stringFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedIntegerFields()
    {
        return $this->integerFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedTextAreaFields()
    {
        return $this->textAreaFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedAttachmentFields()
    {
        return $this->attachmentFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedSingleSelectFields()
    {
        return $this->singleSelectFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    function getUnremovedMultiSelectFields()
    {
        return $this->multiSelectFields->matching($this->nonRemovedCriteria())->getIterator();
    }

    protected function __construct()
    {
        ;
    }
    
    protected function nonRemovedCriteria()
    {
        return Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
    }

}
