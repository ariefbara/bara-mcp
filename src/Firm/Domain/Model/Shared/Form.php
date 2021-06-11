<?php

namespace Firm\Domain\Model\Shared;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\AttachmentField;
use Firm\Domain\Model\Shared\Form\IntegerField;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
use Firm\Domain\Model\Shared\Form\StringField;
use Firm\Domain\Model\Shared\Form\TextAreaField;
use Firm\Domain\Task\BioSearchFilterDataBuilder\BioFormSearchFilterRequest;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;

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

    public function setFieldFiltersToBioSearchFilterData(
            BioSearchFilterData $bioSearchFilterData, BioFormSearchFilterRequest $bioFormSearchFilterRequest): void
    {
        foreach ($bioFormSearchFilterRequest->getIntegerFieldSearchFilterRequests() as $integerFieldId => $comparisonType) {
            $bioSearchFilterData->addIntegerFieldFilter($this->findIntegerFieldOrDie($integerFieldId), $comparisonType);
        }
        foreach ($bioFormSearchFilterRequest->getStringFieldSearchFilterRequests() as $stringFieldId => $comparisonType) {
            $bioSearchFilterData->addStringFieldFilter($this->findStringFieldOrDie($stringFieldId), $comparisonType);
        }
        foreach ($bioFormSearchFilterRequest->getTextAreaFieldSearchFilterRequests() as $textAreaFieldId => $comparisonType) {
            $bioSearchFilterData->addTextAreaFieldFilter(
                    $this->findTextAreaFieldOrDie($textAreaFieldId), $comparisonType);
        }
        foreach ($bioFormSearchFilterRequest->getSingleSelectFieldSearchFilterRequests() as $singleSelectFieldId => $comparisonType) {
            $bioSearchFilterData->addSingleSelectFieldFilter(
                    $this->findSingleSelectFieldOrDie($singleSelectFieldId), $comparisonType);
        }
        foreach ($bioFormSearchFilterRequest->getMultiSelectFieldSearchFilterRequests() as $multiSelectFieldId => $comparisonType) {
            $bioSearchFilterData->addMultiSelectFieldFilter(
                    $this->findMultiSelectFieldOrDie($multiSelectFieldId), $comparisonType);
        }
    }

    protected function findIntegerFieldOrDie(string $integerFieldId): IntegerField
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $integerFieldId));
        $integerField = $this->integerFields->matching($criteria)->first();
        if (empty($integerField)) {
            throw RegularException::notFound('not found: field not found');
        }
        return $integerField;
    }

    protected function findStringFieldOrDie(string $stringFieldId): StringField
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $stringFieldId));
        $stringField = $this->stringFields->matching($criteria)->first();
        if (empty($stringField)) {
            throw RegularException::notFound('not found: field not found');
        }
        return $stringField;
    }

    protected function findTextAreaFieldOrDie(string $textAreaFieldId): TextAreaField
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $textAreaFieldId));
        $textAreaField = $this->textAreaFields->matching($criteria)->first();
        if (empty($textAreaField)) {
            throw RegularException::notFound('not found: field not found');
        }
        return $textAreaField;
    }

    protected function findSingleSelectFieldOrDie(string $singleSelectFieldId): SingleSelectField
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $singleSelectFieldId));
        $singleSelectField = $this->singleSelectFields->matching($criteria)->first();
        if (empty($singleSelectField)) {
            throw RegularException::notFound('not found: field not found');
        }
        return $singleSelectField;
    }

    protected function findMultiSelectFieldOrDie(string $multiSelectFieldId): MultiSelectField
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $multiSelectFieldId));
        $multiSelectField = $this->multiSelectFields->matching($criteria)->first();
        if (empty($multiSelectField)) {
            throw RegularException::notFound('not found: field not found');
        }
        return $multiSelectField;
    }

}
