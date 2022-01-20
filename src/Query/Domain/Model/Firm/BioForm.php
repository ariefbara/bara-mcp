<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Shared\ContainFormInterface;
use Query\Domain\Model\Shared\Form;

class BioForm implements ContainFormInterface
{

    /**
     * 
     * @var Firm
     */
    protected $firm;

    /**
     * 
     * @var Form
     */
    protected $form;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }
    
    public function getId(): string
    {
        return $this->form->getId();
    }

    public function getDescription(): ?string
    {
        return $this->form->getDescription();
    }

    public function getName(): string
    {
        return $this->form->getName();
    }

    public function getUnremovedAttachmentFields()
    {
        return $this->form->getUnremovedAttachmentFields();
    }

    public function getUnremovedIntegerFields()
    {
        return $this->form->getUnremovedIntegerFields();
    }

    public function getUnremovedMultiSelectFields()
    {
        return $this->form->getUnremovedMultiSelectFields();
    }

    public function getUnremovedSingleSelectFields()
    {
        return $this->form->getUnremovedSingleSelectFields();
    }

    public function getUnremovedStringFields()
    {
        return $this->form->getUnremovedStringFields();
    }

    public function getUnremovedTextAreaFields()
    {
        return $this->form->getUnremovedTextAreaFields();
    }

    public function getUnremovedSections()
    {
        return $this->form->getUnremovedSections();
    }

}
