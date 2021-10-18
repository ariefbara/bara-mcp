<?php

namespace SharedContext\Domain\Model\SharedEntity;

use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Resources\{
    DateTimeImmutableBuilder,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\{
    Form\AttachmentField,
    Form\IntegerField,
    Form\MultiSelectField,
    Form\SelectField\Option,
    Form\SingleSelectField,
    Form\StringField,
    Form\TextAreaField,
    FormRecord\AttachmentFieldRecord,
    FormRecord\IntegerFieldRecord,
    FormRecord\MultiSelectFieldRecord,
    FormRecord\SingleSelectFieldRecord,
    FormRecord\StringFieldRecord,
    FormRecord\TextAreaFieldRecord
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

    function getId(): string
    {
        return $this->id;
    }

    public function __construct(Form $form, string $id, FormRecordData $formRecordData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();

        $this->integerFieldRecords = new ArrayCollection();
        $this->stringFieldRecords = new ArrayCollection();
        $this->textAreaFieldRecords = new ArrayCollection();
        $this->singleSelectFieldRecords = new ArrayCollection();
        $this->selectFieldRecords = new ArrayCollection();
        $this->multiSelectFieldRecords = new ArrayCollection();
        $this->attachmentFieldRecords = new ArrayCollection();

        $this->form->setFieldRecordsOf($this, $formRecordData);
    }

    public function update(FormRecordData $formRecordData): void
    {
        $this->form->setFieldRecordsOf($this, $formRecordData);

        $this->removeAllObsoleteStringFieldRecord();
        $this->removeAllObsoleteIntegerFieldRecord();
        $this->removeAllObsoleteTextAreaFieldRecord();
        $this->removeAllObsoleteSingleSelectFieldRecord();
        $this->removeAllObsoleteMultiSelectFieldRecord();
        $this->removeAllObsoleteAttachmentFieldRecord();
    }

    protected function removeAllObsoleteStringFieldRecord(): void
    {
        $p = function (StringFieldRecord $stringFieldRecord) {
            return $stringFieldRecord->isReferToRemovedField() && !$stringFieldRecord->isRemoved();
        };
        foreach ($this->stringFieldRecords->filter($p)->getIterator() as $stringFieldRecord) {
            $stringFieldRecord->remove();
        }
    }

    protected function removeAllObsoleteIntegerFieldRecord(): void
    {
        $p = function (IntegerFieldRecord $integerFieldRecord) {
            return $integerFieldRecord->isReferToRemovedField() && !$integerFieldRecord->isRemoved();
        };
        foreach ($this->integerFieldRecords->filter($p)->getIterator() as $integerFieldRecord) {
            $integerFieldRecord->remove();
        }
    }

    protected function removeAllObsoleteTextAreaFieldRecord(): void
    {
        $p = function (TextAreaFieldRecord $textAreaFieldRecord) {
            return $textAreaFieldRecord->isReferToRemovedField() && !$textAreaFieldRecord->isRemoved();
        };
        foreach ($this->textAreaFieldRecords->filter($p)->getIterator() as $textAreaFieldRecord) {
            $textAreaFieldRecord->remove();
        }
    }

    protected function removeAllObsoleteSingleSelectFieldRecord(): void
    {
        $p = function (SingleSelectFieldRecord $singleSelectFieldRecord) {
            return $singleSelectFieldRecord->isReferToRemovedField() && !$singleSelectFieldRecord->isRemoved();
        };
        foreach ($this->singleSelectFieldRecords->filter($p)->getIterator() as $singleSelectFieldRecord) {
            $singleSelectFieldRecord->remove();
        }
    }

    protected function removeAllObsoleteMultiSelectFieldRecord(): void
    {
        $p = function (MultiSelectFieldRecord $multiSelectFieldRecord) {
            return $multiSelectFieldRecord->isReferToRemovedField() && !$multiSelectFieldRecord->isRemoved();
        };
        foreach ($this->multiSelectFieldRecords->filter($p)->getIterator() as $multiSelectFieldRecord) {
            $multiSelectFieldRecord->remove();
        }
    }

    protected function removeAllObsoleteAttachmentFieldRecord(): void
    {
        $p = function (AttachmentFieldRecord $attachmentFieldRecord) {
            return $attachmentFieldRecord->isReferToRemovedField() && !$attachmentFieldRecord->isRemoved();
        };
        foreach ($this->attachmentFieldRecords->filter($p)->getIterator() as $attachmentFieldRecord) {
            $attachmentFieldRecord->remove();
        }
    }

    public function setStringFieldRecord(StringField $stringField, ?string $value): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('stringField', $stringField))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $existingStringFieldRecord = $this->stringFieldRecords->matching($criteria)->first();
        if (!empty($existingStringFieldRecord)) {
            $existingStringFieldRecord->update($value);
        } else {
            $id = Uuid::generateUuid4();
            $newStringFieldRecord = new StringFieldRecord($this, $id, $stringField, $value);
            $this->stringFieldRecords->add($newStringFieldRecord);
        }
    }

    public function setIntegerFieldRecord(IntegerField $integerField, ?float $value): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('integerField', $integerField))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $existingIntegerFieldRecord = $this->integerFieldRecords->matching($criteria)->first();
        if (!empty($existingIntegerFieldRecord)) {
            $existingIntegerFieldRecord->update($value);
        } else {
            $id = Uuid::generateUuid4();
            $integerFieldRecord = new IntegerFieldRecord($this, $id, $integerField, $value);
            $this->integerFieldRecords->add($integerFieldRecord);
        }
    }

    public function setTextAreaFieldRecord(TextAreaField $textAreaField, ?string $value): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('textAreaField', $textAreaField))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $existingTextAreaFieldRecord = $this->textAreaFieldRecords->matching($criteria)->first();

        if (!empty($existingTextAreaFieldRecord)) {
            $existingTextAreaFieldRecord->update($value);
        } else {
            $id = Uuid::generateUuid4();
            $textAreaFieldRecord = new TextAreaFieldRecord($this, $id, $textAreaField, $value);
            $this->textAreaFieldRecords->add($textAreaFieldRecord);
        }
    }

    public function setSingleSelectFieldRecord(SingleSelectField $singleSelectField, ?Option $selectedOption): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('singleSelectField', $singleSelectField))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $existingSingleSelectFieldRecord = $this->singleSelectFieldRecords->matching($criteria)->first();

        if (!empty($existingSingleSelectFieldRecord)) {
            $existingSingleSelectFieldRecord->update($selectedOption);
        } else {
            $id = Uuid::generateUuid4();
            $singleSelectFieldRecord = new SingleSelectFieldRecord($this, $id, $singleSelectField, $selectedOption);
            $this->singleSelectFieldRecords->add($singleSelectFieldRecord);
        }
    }

    public function setMultiSelectFieldRecord(MultiSelectField $multiSelectField, array $selectedOptions): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('multiSelectField', $multiSelectField))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $existingMultiSelectFieldRecord = $this->multiSelectFieldRecords->matching($criteria)->first();
        if (!empty($existingMultiSelectFieldRecord)) {
            $existingMultiSelectFieldRecord->setSelectedOptions($selectedOptions);
        } else {
            $id = Uuid::generateUuid4();
            $multiSelectFieldRecord = new MultiSelectFieldRecord($this, $id, $multiSelectField, $selectedOptions);
            $this->multiSelectFieldRecords->add($multiSelectFieldRecord);
        }
    }

    public function setAttachmentFieldRecord(AttachmentField $attachmentField, array $fileInfoList): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('attachmentField', $attachmentField))
                ->andWhere(Criteria::expr()->eq('removed', false))
                ->setMaxResults(1);
        $existingAttachmentFieldRecord = $this->attachmentFieldRecords->matching($criteria)->first();
        if (!empty($existingAttachmentFieldRecord)) {
            $existingAttachmentFieldRecord->setAttachedFiles($fileInfoList);
        } else {
            $id = Uuid::generateUuid4();
            $attachmentFieldRecord = new AttachmentFieldRecord($this, $id, $attachmentField, $fileInfoList);
            $this->attachmentFieldRecords->add($attachmentFieldRecord);
        }
    }

}
