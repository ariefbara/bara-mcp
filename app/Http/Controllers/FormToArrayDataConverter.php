<?php

namespace App\Http\Controllers;

use Firm\Domain\Model\Shared\ {
    ComposedOfForm,
    Form\AttachmentField,
    Form\IntegerField,
    Form\MultiSelectField,
    Form\SelectField\Option,
    Form\SingleSelectField,
    Form\StringField,
    Form\TextAreaField
};

class FormToArrayDataConverter
{
    public function __construct()
    {
        ;
    }

    public function convert(ComposedOfForm $composedOfForm): array
    {
        $data = [
            "name" => $composedOfForm->getName(),
            "description" => $composedOfForm->getDescription(),
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
        ];

        foreach ($composedOfForm->getUnremovedStringFields() as $stringField) {
            $data["stringFields"][] = $this->arrayDataOfStringField($stringField);
        }
        foreach ($composedOfForm->getUnremovedIntegerFields() as $integerField) {
            $data['integerFields'][] = $this->arrayDataOfIntegerField($integerField);
        }
        foreach ($composedOfForm->getUnremovedTextAreaFields() as $textAreaField) {
            $data['textAreaFields'][] = $this->arrayDataOfTextAreaField($textAreaField);
        }
        foreach ($composedOfForm->getUnremovedSingleSelectFields() as $singleSelectField) {
            $data['singleSelectFields'][] = $this->arrayDataOfSingleSelectField($singleSelectField);
        }
        foreach ($composedOfForm->getUnremovedMultiSelectFields() as $multiSelectField) {
            $data['multiSelectFields'][] = $this->arrayDataOfMultiSelectField($multiSelectField);
        }
        foreach ($composedOfForm->getUnremovedAttachmentFields() as $attachmentField) {
            $data['attachmentFields'][] = $this->arrayDataOfAttachmentField($attachmentField);
        }

        return $data;
    }

    protected function arrayDataOfStringField(StringField $stringField): array
    {
        return [
            "id" => $stringField->getId(),
            "name" => $stringField->getName(),
            "description" => $stringField->getDescription(),
            "position" => $stringField->getPosition(),
            "mandatory" => $stringField->isMandatory(),
            "defaultValue" => $stringField->getDefaultValue(),
            "minValue" => $stringField->getMinValue(),
            "maxValue" => $stringField->getMaxValue(),
            "placeholder" => $stringField->getPlaceholder(),
        ];
    }

    protected function arrayDataOfIntegerField(IntegerField $integerField): array
    {
        return [
            "id" => $integerField->getId(),
            "name" => $integerField->getName(),
            "description" => $integerField->getDescription(),
            "position" => $integerField->getPosition(),
            "mandatory" => $integerField->isMandatory(),
            "defaultValue" => $integerField->getDefaultValue(),
            "minValue" => $integerField->getMinValue(),
            "maxValue" => $integerField->getMaxValue(),
            "placeholder" => $integerField->getPlaceholder(),
        ];
    }

    protected function arrayDataOfTextAreaField(TextAreaField $textAreaField): array
    {
        return [
            "id" => $textAreaField->getId(),
            "name" => $textAreaField->getName(),
            "description" => $textAreaField->getDescription(),
            "position" => $textAreaField->getPosition(),
            "mandatory" => $textAreaField->isMandatory(),
            "defaultValue" => $textAreaField->getDefaultValue(),
            "minValue" => $textAreaField->getMinValue(),
            "maxValue" => $textAreaField->getMaxValue(),
            "placeholder" => $textAreaField->getPlaceholder(),
        ];
    }

    protected function arrayDataOfSingleSelectField(SingleSelectField $singleSelectField): array
    {
        $singleSelectFieldData = [
            "id" => $singleSelectField->getId(),
            "name" => $singleSelectField->getName(),
            "description" => $singleSelectField->getDescription(),
            "position" => $singleSelectField->getPosition(),
            "mandatory" => $singleSelectField->isMandatory(),
            "defaultValue" => $singleSelectField->getDefaultValue(),
            "options" => [],
        ];
        foreach ($singleSelectField->getUnremovedOptions() as $option) {
            $singleSelectFieldData['options'][] = $this->arrayDataOfOption($option);
        }
        return $singleSelectFieldData;
    }

    protected function arrayDataOfMultiSelectField(MultiSelectField $multiSelectField): array
    {
        $multiSelectFieldData = [
            "id" => $multiSelectField->getId(),
            "name" => $multiSelectField->getName(),
            "description" => $multiSelectField->getDescription(),
            "position" => $multiSelectField->getPosition(),
            "mandatory" => $multiSelectField->isMandatory(),
            "minValue" => $multiSelectField->getMinValue(),
            "maxValue" => $multiSelectField->getMaxValue(),
            "options" => [],
        ];
        foreach ($multiSelectField->getUnremovedOptions() as $option) {
            $multiSelectFieldData['options'][] = $this->arrayDataOfOption($option);
        }
        return $multiSelectFieldData;
    }

    protected function arrayDataOfAttachmentField(AttachmentField $attachmentField): array
    {
        return [
            "id" => $attachmentField->getId(),
            "name" => $attachmentField->getName(),
            "description" => $attachmentField->getDescription(),
            "position" => $attachmentField->getPosition(),
            "mandatory" => $attachmentField->isMandatory(),
            "minValue" => $attachmentField->getMinValue(),
            "maxValue" => $attachmentField->getMaxValue(),
        ];
    }

    protected function arrayDataOfOption(Option $option): array
    {
        return [
            "id" => $option->getId(),
            "name" => $option->getName(),
            "description" => $option->getDescription(),
            "position" => $option->getPosition(),
        ];
    }

}
