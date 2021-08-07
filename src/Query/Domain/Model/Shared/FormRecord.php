<?php

namespace Query\Domain\Model\Shared;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord;
use Query\Domain\Model\Shared\FormRecord\IntegerFieldRecord;
use Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\SingleSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\StringFieldRecord;
use Query\Domain\Model\Shared\FormRecord\TextAreaFieldRecord;

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
        return isset($this->submitTime) ? $this->submitTime->format("Y-m-d H:i:s") : null;
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

    public function toArray(): array
    {
        $result = [
            "submitTime" => $this->getSubmitTimeString(),
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        foreach ($this->getUnremovedStringFieldRecords() as $stringFieldRecord) {
            $result['stringFieldRecords'][] = $this->arrayDataOfStringFieldRecord($stringFieldRecord);
        }
        foreach ($this->getUnremovedIntegerFieldRecords() as $integerFieldRecord) {
            $result['integerFieldRecords'][] = $this->arrayDataOfIntegerFieldRecord($integerFieldRecord);
        }
        foreach ($this->getUnremovedTextAreaFieldRecords() as $textAreaFieldRecord) {
            $result['textAreaFieldRecords'][] = $this->arrayDataOfTextAreaFieldRecord($textAreaFieldRecord);
        }
        foreach ($this->getUnremovedAttachmentFieldRecords() as $attachmentFieldRecord) {
            $result['attachmentFieldRecords'][] = $this->arrayDataOfAttachmentFieldRecord($attachmentFieldRecord);
        }
        foreach ($this->getUnremovedSingleSelectFieldRecords() as $singleSelectFieldRecord) {
            $result['singleSelectFieldRecords'][] = $this->arrayDataOfSingleSelectFieldRecord($singleSelectFieldRecord);
        }
        foreach ($this->getUnremovedMultiSelectFieldRecords() as $multiSelectFieldRecord) {
            $result['multiSelectFieldRecords'][] = $this->arrayDataOfMultiSelectFieldRecord($multiSelectFieldRecord);
        }
        return $result;
    }

    private function arrayDataOfStringFieldRecord(StringFieldRecord $stringFieldRecord): array
    {
        return [
            "id" => $stringFieldRecord->getId(),
            "stringField" => [
                "id" => $stringFieldRecord->getStringField()->getId(),
                "name" => $stringFieldRecord->getStringField()->getName(),
                "position" => $stringFieldRecord->getStringField()->getPosition(),
            ],
            "value" => $stringFieldRecord->getValue(),
        ];
    }

    private function arrayDataOfIntegerFieldRecord(IntegerFieldRecord $integerFieldRecord): array
    {
        return [
            "id" => $integerFieldRecord->getId(),
            "integerField" => [
                "id" => $integerFieldRecord->getIntegerField()->getId(),
                "name" => $integerFieldRecord->getIntegerField()->getName(),
                "position" => $integerFieldRecord->getIntegerField()->getPosition(),
            ],
            "value" => $integerFieldRecord->getValue(),
        ];
    }

    private function arrayDataOfTextAreaFieldRecord(TextAreaFieldRecord $textAreaFieldRecord): array
    {
        return [
            "id" => $textAreaFieldRecord->getId(),
            "textAreaField" => [
                "id" => $textAreaFieldRecord->getTextAreaField()->getId(),
                "name" => $textAreaFieldRecord->getTextAreaField()->getName(),
                "position" => $textAreaFieldRecord->getTextAreaField()->getPosition(),
            ],
            "value" => $textAreaFieldRecord->getValue(),
        ];
    }

    private function arrayDataOfSingleSelectFieldRecord(SingleSelectFieldRecord $singleSelectFieldRecord): array
    {
        $selectedOption = empty($singleSelectFieldRecord->getOption()) ? null :
                [
            "id" => $singleSelectFieldRecord->getOption()->getId(),
            "name" => $singleSelectFieldRecord->getOption()->getName(),
        ];
        return [
            "id" => $singleSelectFieldRecord->getId(),
            "singleSelectField" => [
                "id" => $singleSelectFieldRecord->getSingleSelectField()->getId(),
                "name" => $singleSelectFieldRecord->getSingleSelectField()->getName(),
                "position" => $singleSelectFieldRecord->getSingleSelectField()->getPosition(),
            ],
            "selectedOption" => $selectedOption,
        ];
    }

    private function arrayDataOfMultiSelectFieldRecord(MultiSelectFieldRecord $multiSelectFieldRecord): array
    {
        $multiSelectFieldRecordData = [
            "id" => $multiSelectFieldRecord->getId(),
            "multiSelectField" => [
                "id" => $multiSelectFieldRecord->getMultiSelectField()->getId(),
                "name" => $multiSelectFieldRecord->getMultiSelectField()->getName(),
                "position" => $multiSelectFieldRecord->getMultiSelectField()->getPosition(),
            ],
            "selectedOptions" => [],
        ];
        foreach ($multiSelectFieldRecord->getUnremovedSelectedOptions() as $selectedOption) {
            $multiSelectFieldRecordData['selectedOptions'][] = [
                "id" => $selectedOption->getId(),
                "option" => [
                    "id" => $selectedOption->getOption()->getId(),
                    "name" => $selectedOption->getOption()->getName(),
                ],
            ];
        }
        return $multiSelectFieldRecordData;
    }

    private function arrayDataOfAttachmentFieldRecord(AttachmentFieldRecord $attachmentFieldRecord): array
    {
        $attachmentFieldRecordData = [
            "id" => $attachmentFieldRecord->getId(),
            "attachmentField" => [
                "id" => $attachmentFieldRecord->getAttachmentField()->getId(),
                "name" => $attachmentFieldRecord->getAttachmentField()->getName(),
                "position" => $attachmentFieldRecord->getAttachmentField()->getPosition(),
            ],
            "attachedFiles" => [],
        ];
        foreach ($attachmentFieldRecord->getUnremovedAttachedFiles() as $attachedFile) {
            $attachmentFieldRecordData['attachedFiles'][] = [
                "id" => $attachedFile->getId(),
                "fileInfo" => [
                    "id" => $attachedFile->getFileInfo()->getId(),
                    "path" => $attachedFile->getFileInfo()->getFullyQualifiedFileName(),
                ],
            ];
        }
        return $attachmentFieldRecordData;
    }

}
