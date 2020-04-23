<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\{
    Firm,
    Shared\ComposedOfForm,
    Shared\Form,
    Shared\FormData
};

class WorksheetForm implements ComposedOfForm
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

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Firm $firm, string $id, Form $form)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->form = $form;
        $this->removed = false;
    }

    public function update(FormData $formData): void
    {
        $this->form->update($formData);
    }

    public function remove(): void
    {
        $this->removed = true;
    }

    public function getDescription(): string
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

}
