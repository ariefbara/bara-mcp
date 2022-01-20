<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Shared\ContainFormInterface;
use Query\Domain\Model\Shared\Form;

class ProfileForm implements ContainFormInterface
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Form
     */
    protected $form;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
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
