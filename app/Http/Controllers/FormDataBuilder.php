<?php

namespace App\Http\Controllers;

use Firm\Domain\Model\Shared\Form\AttachmentFieldData;
use Firm\Domain\Model\Shared\Form\FieldData;
use Firm\Domain\Model\Shared\Form\IntegerFieldData;
use Firm\Domain\Model\Shared\Form\MultiSelectFieldData;
use Firm\Domain\Model\Shared\Form\SectionData;
use Firm\Domain\Model\Shared\Form\SelectField\OptionData;
use Firm\Domain\Model\Shared\Form\SelectFieldData;
use Firm\Domain\Model\Shared\Form\SingleSelectFieldData;
use Firm\Domain\Model\Shared\Form\StringFieldData;
use Firm\Domain\Model\Shared\Form\TextAreaFieldData;
use Firm\Domain\Model\Shared\FormData;
use Illuminate\Http\Request;

class FormDataBuilder
{
    /**
     *
     * @var Request
     */
    protected $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    protected function stripTagsVariable($var): ?string
    {
        return isset($var) ? htmlentities($var, ENT_QUOTES, 'UTF-8') : null;
    }

    protected function integerOfVariable($var): ?int
    {
        return isset($var) ? (int) $var : null;
    }
    
    public function build(): FormData
    {
        $name = $this->stripTagsVariable($this->request->input('name'));
        $description = $this->request->input('description');
        $formData = new FormData($name, $description);
        $this->attachStringFieldDataToFormData($formData);
        $this->attachIntegerFieldDataToFormData($formData);
        $this->attachTextAreaFieldDataToFormData($formData);
        $this->attachAttachmentFieldDataToFormData($formData);
        $this->attachSingleSelectFieldDataToFormData($formData);
        $this->attachMultiSelectFieldDataToFormData($formData);
        $this->attachSectionDataToFormData($formData);
        return $formData;
    }
    protected function attachStringFieldDataToFormData(FormData $formData): void
    {
        foreach ($this->request->input('stringFields') as $stringFieldRequest) {
            $name = $this->stripTagsVariable($stringFieldRequest['name']);
            $description = $this->stripTagsVariable($stringFieldRequest['description']);
            $position = $this->stripTagsVariable($stringFieldRequest['position']);
            $mandatory = filter_var($stringFieldRequest['mandatory'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            $fieldData = new FieldData($name, $description, $position, $mandatory);
            $minValue = $this->integerOfVariable($stringFieldRequest['minValue']);
            $maxValue = $this->integerOfVariable($stringFieldRequest['maxValue']);
            $placeholder = $this->stripTagsVariable($stringFieldRequest['placeholder']);
            $defaultValue = $this->stripTagsVariable($stringFieldRequest['defaultValue']);
            
            $stringFieldData = new StringFieldData($fieldData, $minValue, $maxValue, $placeholder, $defaultValue);
            $stringFieldId = (isset($stringFieldRequest['id']))? htmlentities($stringFieldRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            
            $formData->pushStringFieldData($stringFieldData, $stringFieldId);
        }
    }
    protected function attachIntegerFieldDataToFormData(FormData $formData): void
    {
        foreach ($this->request->input('integerFields') as $integerFieldRequest) {
            $name = $this->stripTagsVariable($integerFieldRequest['name']);
            $description = $this->stripTagsVariable($integerFieldRequest['description']);
            $position = $this->stripTagsVariable($integerFieldRequest['position']);
            $mandatory = filter_var($integerFieldRequest['mandatory'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            $fieldData = new FieldData($name, $description, $position, $mandatory);
            $minValue = $this->integerOfVariable($integerFieldRequest['minValue']);
            $maxValue = $this->integerOfVariable($integerFieldRequest['maxValue']);
            $placeholder = $this->stripTagsVariable($integerFieldRequest['placeholder']);
            $defaultValue = filter_var($integerFieldRequest['defaultValue'], FILTER_SANITIZE_NUMBER_INT);
            
            $integerFieldData = new IntegerFieldData($fieldData, $minValue, $maxValue, $placeholder, $defaultValue);
            $integerFieldId = (isset($integerFieldRequest['id']))? htmlentities($integerFieldRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            
            $formData->pushIntegerFieldData($integerFieldData, $integerFieldId);
        }
    }
    protected function attachTextAreaFieldDataToFormData(FormData $formData): void
    {
        foreach ($this->request->input('textAreaFields') as $textAreaFieldRequest) {
            $name = $this->stripTagsVariable($textAreaFieldRequest['name']);
            $description = $this->stripTagsVariable($textAreaFieldRequest['description']);
            $position = $this->stripTagsVariable($textAreaFieldRequest['position']);
            $mandatory = filter_var($textAreaFieldRequest['mandatory'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            $fieldData = new FieldData($name, $description, $position, $mandatory);
            $minValue = $this->integerOfVariable($textAreaFieldRequest['minValue']);
            $maxValue = $this->integerOfVariable($textAreaFieldRequest['maxValue']);
            $placeholder = $this->stripTagsVariable($textAreaFieldRequest['placeholder']);
            $defaultValue = $this->stripTagsVariable($textAreaFieldRequest['defaultValue']);
            
            $textAreaFieldData = new TextAreaFieldData($fieldData, $minValue, $maxValue, $placeholder, $defaultValue);
            $textAreaFieldId = (isset($textAreaFieldRequest['id']))? htmlentities($textAreaFieldRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            
            $formData->pushTextAreaFieldData($textAreaFieldData, $textAreaFieldId);
        }
    }
    protected function attachAttachmentFieldDataToFormData(FormData $formData): void
    {
        foreach ($this->request->input('attachmentFields') as $attachmentFieldRequest) {
            $name = $this->stripTagsVariable($attachmentFieldRequest['name']);
            $description = $this->stripTagsVariable($attachmentFieldRequest['description']);
            $position = $this->stripTagsVariable($attachmentFieldRequest['position']);
            $mandatory = filter_var($attachmentFieldRequest['mandatory'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            $fieldData = new FieldData($name, $description, $position, $mandatory);
            $minValue = $this->integerOfVariable($attachmentFieldRequest['minValue']);
            $maxValue = $this->integerOfVariable($attachmentFieldRequest['maxValue']);
            
            $attachmentFieldData = new AttachmentFieldData($fieldData, $minValue, $maxValue);
            $attachmentFieldId = (isset($attachmentFieldRequest['id']))? htmlentities($attachmentFieldRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            $formData->pushAttachmentFieldData($attachmentFieldData, $attachmentFieldId);
        }
    }
    protected function attachSingleSelectFieldDataToFormData(FormData $formData): void
    {
        foreach ($this->request->input('singleSelectFields') as $singleSelectFieldRequest) {
            $name = $this->stripTagsVariable($singleSelectFieldRequest['name']);
            $description = $this->stripTagsVariable($singleSelectFieldRequest['description']);
            $position = $this->stripTagsVariable($singleSelectFieldRequest['position']);
            $mandatory = filter_var($singleSelectFieldRequest['mandatory'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            $fieldData = new FieldData($name, $description, $position, $mandatory);
            $selectFieldData = new SelectFieldData($fieldData);
            $this->attachOptionDataToSelectField($selectFieldData, $singleSelectFieldRequest['options']);
            $defaultValue = $this->stripTagsVariable($singleSelectFieldRequest['defaultValue']);
            
            $singleSelectFieldData = new SingleSelectFieldData($selectFieldData, $defaultValue);
            $singleSelectFieldId = (isset($singleSelectFieldRequest['id']))? htmlentities($singleSelectFieldRequest['id'], ENT_QUOTES, 'UTF-8'): null;
                    
            $formData->pushSingleSelectFieldData($singleSelectFieldData, $singleSelectFieldId);
        }
    }
    protected function attachMultiSelectFieldDataToFormData(FormData $formData): void
    {
        foreach ($this->request->input('multiSelectFields') as $multiSelectFieldRequest) {
            $name = $this->stripTagsVariable($multiSelectFieldRequest['name']);
            $description = $this->stripTagsVariable($multiSelectFieldRequest['description']);
            $position = $this->stripTagsVariable($multiSelectFieldRequest['position']);
            $mandatory = filter_var($multiSelectFieldRequest['mandatory'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            
            $fieldData = new FieldData($name, $description, $position, $mandatory);
            $selectFieldData = new SelectFieldData($fieldData);
            $this->attachOptionDataToSelectField($selectFieldData, $multiSelectFieldRequest['options']);
            $minValue = $this->integerOfVariable($multiSelectFieldRequest['minValue']);
            $maxValue = $this->integerOfVariable($multiSelectFieldRequest['maxValue']);
            
            $multiSelectFieldData = new MultiSelectFieldData($selectFieldData, $minValue, $maxValue);
            $multiSelectFieldId = (isset($multiSelectFieldRequest['id']))? htmlentities($multiSelectFieldRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            
            $formData->pushMultiSelectFieldData($multiSelectFieldData, $multiSelectFieldId);
        }
    }
    protected function attachOptionDataToSelectField(SelectFieldData $selectFieldData, array $optionsRequest): void
    {
        foreach ($optionsRequest as $optionRequest) {
            $name = $this->stripTagsVariable($optionRequest['name']);
            $description = $this->stripTagsVariable($optionRequest['description']);
            $position = $this->stripTagsVariable($optionRequest['position']);
            
            $optionData = new OptionData($name, $description, $position);
            $optionId = (isset($optionRequest['id']))? htmlentities($optionRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            
            $selectFieldData->pushOptionData($optionData, $optionId);
        }
    }
    protected function attachSectionDataToFormData(FormData $formData): void
    {
        if (!$this->request->exists('sections')) {
            return;
        }
        foreach ($this->request->input('sections') as $sectionRequest) {
            $name = $this->stripTagsVariable($sectionRequest['name']);
            $position = $this->stripTagsVariable($sectionRequest['position']);
            
            $sectionData = new SectionData($name, $position);
            $sectionId = (isset($sectionRequest['id']))? htmlentities($sectionRequest['id'], ENT_QUOTES, 'UTF-8'): null;
            
            $formData->pushSectionData($sectionData, $sectionId);
        }
    }
}
