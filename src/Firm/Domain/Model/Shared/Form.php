<?php

namespace Firm\Domain\Model\Shared;

use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Firm\Domain\Model\Shared\Form\{
    AttachmentField,
    IntegerField,
    MultiSelectField,
    SingleSelectField,
    StringField,
    TextAreaField
};
use Resources\{
    Uuid,
    ValidationRule,
    ValidationService
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

    protected function setName(string $name): void
    {
        $errorDetail = 'bad request: form name is mandatory';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    public function __construct(string $id, FormData $formData)
    {
        $this->id = $id;
        $this->setName($formData->getName());
        $this->description = $formData->getDescription();

        $this->stringFields = new ArrayCollection();
        $this->integerFields = new ArrayCollection();
        $this->textAreaFields = new ArrayCollection();
        $this->singleSelectFields = new ArrayCollection();
        $this->multiSelectFields = new ArrayCollection();
        $this->attachmentFields = new ArrayCollection();

        $this->addStringFields($formData);
        $this->addIntegerFields($formData);
        $this->addTextAreaFields($formData);
        $this->addSingleSelectFields($formData);
        $this->addMultiSelectFields($formData);
        $this->addAttachmentFields($formData);
    }

    public function update(FormData $formData): void
    {
        $this->setName($formData->getName());
        $this->description = $formData->getDescription();

        $this->setStringFields($formData);
        $this->setIntegerFields($formData);
        $this->setTextAreaFields($formData);
        $this->setSingleSelectFields($formData);
        $this->setMultiSelectFields($formData);
        $this->setAttachmentFields($formData);
    }

    protected function addStringFields(FormData $formData): void
    {
        foreach ($formData->getStringFieldDataCollection() as $stringFieldData) {
            $id = Uuid::generateUuid4();
            $stringField = new StringField($this, $id, $stringFieldData);
            $this->stringFields->add($stringField);
        }
    }

    protected function addIntegerFields(FormData $formData): void
    {
        foreach ($formData->getIntegerFieldDataCollection() as $integerFieldData) {
            $id = Uuid::generateUuid4();
            $integerField = new IntegerField($this, $id, $integerFieldData);
            $this->integerFields->add($integerField);
        }
    }

    protected function addTextAreaFields(FormData $formData): void
    {
        foreach ($formData->getTextAreaFieldDataCollection() as $textAreaFieldData) {
            $id = Uuid::generateUuid4();
            $textAreaField = new TextAreaField($this, $id, $textAreaFieldData);
            $this->textAreaFields->add($textAreaField);
        }
    }

    protected function addSingleSelectFields(FormData $formData): void
    {
        foreach ($formData->getSingleSelectFieldDataCollection() as $singleSelectFieldData) {
            $id = Uuid::generateUuid4();
            $singleSelectField = new SingleSelectField($this, $id, $singleSelectFieldData);
            $this->singleSelectFields->add($singleSelectField);
        }
    }

    protected function addMultiSelectFields(FormData $formData): void
    {
        foreach ($formData->getMultiSelectFieldDataCollection() as $multiSelectFieldData) {
            $id = Uuid::generateUuid4();
            $multiSelectField = new MultiSelectField($this, $id, $multiSelectFieldData);
            $this->multiSelectFields->add($multiSelectField);
        }
    }

    protected function addAttachmentFields(FormData $formData): void
    {
        foreach ($formData->getAttachmentFieldDataCollection() as $attachmentFieldData) {
            $id = Uuid::generateUuid4();
            $attachmentField = new AttachmentField($this, $id, $attachmentFieldData);
            $this->attachmentFields->add($attachmentField);
        }
    }

    protected function setStringFields(FormData $formData): void
    {
        foreach ($this->stringFields->matching($this->nonRemovedCriteria())->getIterator() as $stringField) {
            $stringFieldData = $formData->pullStringFieldDataOfId($stringField->getId());
            if ($stringFieldData == null) {
                $stringField->remove();
            } else {
                $stringField->update($stringFieldData);
            }
        }
        $this->addStringFields($formData);
    }

    protected function setIntegerFields(FormData $formData): void
    {
        foreach ($this->integerFields->matching($this->nonRemovedCriteria())->getIterator() as $integerField) {
            $integerFieldData = $formData->pullIntegerFieldDataOfId($integerField->getId());
            if ($integerFieldData == null) {
                $integerField->remove();
            } else {
                $integerField->update($integerFieldData);
            }
        }
        $this->addIntegerFields($formData);
    }

    protected function setTextAreaFields(FormData $formData): void
    {
        foreach ($this->textAreaFields->matching($this->nonRemovedCriteria())->getIterator() as $textAreaField) {
            $textAreaFieldData = $formData->pullTextAreaFieldDataOfId($textAreaField->getId());
            if ($textAreaFieldData == null) {
                $textAreaField->remove();
            } else {
                $textAreaField->update($textAreaFieldData);
            }
        }
        $this->addTextAreaFields($formData);
    }

    protected function setSingleSelectFields(FormData $formData): void
    {
        foreach ($this->singleSelectFields->matching($this->nonRemovedCriteria())->getIterator() as $singleSelectField) {
            $singleSelectFieldData = $formData->pullSingleSelectFieldDataOfId($singleSelectField->getId());
            if ($singleSelectFieldData == null) {
                $singleSelectField->remove();
            } else {
                $singleSelectField->update($singleSelectFieldData);
            }
        }
        $this->addSingleSelectFields($formData);
    }

    protected function setMultiSelectFields(FormData $formData): void
    {
        foreach ($this->multiSelectFields->matching($this->nonRemovedCriteria())->getIterator() as $multiSelectField) {
            $multiSelectFieldData = $formData->pullMultiSelectFieldDataOfId($multiSelectField->getId());
            if ($multiSelectFieldData == null) {
                $multiSelectField->remove();
            } else {
                $multiSelectField->update($multiSelectFieldData);
            }
        }
        $this->addMultiSelectFields($formData);
    }

    protected function setAttachmentFields(FormData $formData): void
    {
        foreach ($this->attachmentFields->matching($this->nonRemovedCriteria())->getIterator() as $attachmentField) {
            $attachmentFieldData = $formData->pullAttachmentFieldDataOfId($attachmentField->getId());
            if ($attachmentFieldData == null) {
                $attachmentField->remove();
            } else {
                $attachmentField->update($attachmentFieldData);
            }
        }
        $this->addAttachmentFields($formData);
    }

    protected function nonRemovedCriteria(): Criteria
    {
        return Criteria::create()
                        ->andWhere(Criteria::expr()->eq('removed', false));
    }

}
