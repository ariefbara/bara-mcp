<?php

namespace App\Http\Controllers;

use Shared\Domain\Model\{
    FormRecord\AttachmentFieldRecord,
    FormRecord\IntegerFieldRecord,
    FormRecord\MultiSelectFieldRecord,
    FormRecord\SingleSelectFieldRecord,
    FormRecord\StringFieldRecord,
    FormRecord\TextAreaFieldRecord,
    HasFormRecordInterface
};

class FormRecordToArrayDataConverter
{

    public function __construct()
    {
        ;
    }

    public function convert(HasFormRecordInterface $formRecord): array
    {
        $data = [
            "submitTime" => $formRecord->getSubmitTimeString(),
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        foreach ($formRecord->getUnremovedStringFieldRecords() as $stringFieldRecord) {
            $data['stringFieldRecords'][] = $this->arrayDataOfStringFieldRecord($stringFieldRecord);
        }
        foreach ($formRecord->getUnremovedIntegerFieldRecords() as $integerFieldRecord) {
            $data['integerFieldRecords'][] = $this->arrayDataOfIntegerFieldRecord($integerFieldRecord);
        }
        foreach ($formRecord->getUnremovedTextAreaFieldRecords() as $textAreaFieldRecord) {
            $data['textAreaFieldRecords'][] = $this->arrayDataOfTextAreaFieldRecord($textAreaFieldRecord);
        }
        foreach ($formRecord->getUnremovedAttachmentFieldRecords() as $attachmentFieldRecord) {
            $data['attachmentFieldRecords'][] = $this->arrayDataOfAttachmentFieldRecord($attachmentFieldRecord);
        }
        foreach ($formRecord->getUnremovedSingleSelectFieldRecords() as $singleSelectFieldRecord) {
            $data['singleSelectFieldRecords'][] = $this->arrayDataOfSingleSelectFieldRecord($singleSelectFieldRecord);
        }
        foreach ($formRecord->getUnremovedMultiSelectFieldRecords() as $multiSelectFieldRecord) {
            $data['multiSelectFieldRecords'][] = $this->arrayDataOfMultiSelectFieldRecord($multiSelectFieldRecord);
        }
        return $data;
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
