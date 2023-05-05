<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecordData,
    FormRecordData\AttachmentFieldRecordData,
    FormRecordData\IFileInfoFinder,
    FormRecordData\MultiSelectFieldRecordData
};

class FormRecordDataBuilder
{

    /**
     *
     * @var Request
     */
    protected $request;

    /**
     *
     * @var IFileInfoFinder
     */
    protected $fileInfoFinder;

    function __construct(Request $request, IFileInfoFinder $fileInfoFinder)
    {
        $this->request = $request;
        $this->fileInfoFinder = $fileInfoFinder;
    }

    protected function stripTagsVariable($var): ?string
    {
        return isset($var) ? $var : null;
    }

    protected function floatOfVariable($var): ?float
    {
        return isset($var) ? (float) $var : null;
    }

    public function build(): FormRecordData
    {
        $formRecordData = new FormRecordData();
        foreach ($this->request->input('stringFieldRecords') ?? [] as $stringFieldRecord) {
            $stringFieldId = $this->stripTagsVariable($stringFieldRecord['fieldId']);
            $input = $this->stripTagsVariable($stringFieldRecord['value']);
            $formRecordData->addStringFieldRecordData($stringFieldId, $input);
        }
        foreach ($this->request->input('integerFieldRecords') ?? [] as $integerFieldRecord) {
            $integerFieldId = $this->stripTagsVariable($integerFieldRecord['fieldId']);
            $input = $this->floatOfVariable($integerFieldRecord['value']);
            $formRecordData->addIntegerFieldRecordData($integerFieldId, $input);
        }
        foreach ($this->request['textAreaFieldRecords']  ?? [] as $textAreaFieldRecord) {
            $textAreaFieldId = $this->stripTagsVariable($textAreaFieldRecord['fieldId']);
            $input = $this->stripTagsVariable($textAreaFieldRecord['value']);
            $formRecordData->addTextAreaFieldRecordData($textAreaFieldId, $input);
        }
        foreach ($this->request['attachmentFieldRecords'] ?? [] as $attachmentFieldRecord) {
            $attachmentFieldId = $this->stripTagsVariable($attachmentFieldRecord['fieldId']);
            $attachmentFieldRecordData = new AttachmentFieldRecordData($this->fileInfoFinder, $attachmentFieldId);
            foreach ($attachmentFieldRecord['fileInfoIdList'] ?? [] as $fileInfoId) {
                $attachmentFieldRecordData->add($fileInfoId);
            }
            $formRecordData->addAttachmentFieldRecordData($attachmentFieldRecordData);
        }
        foreach ($this->request['singleSelectFieldRecords']  ?? [] as $singleSelectFieldRecord) {
            $singleSelectFieldId = $this->stripTagsVariable($singleSelectFieldRecord['fieldId']);
            $selectedOptionId = $this->stripTagsVariable($singleSelectFieldRecord['selectedOptionId']);
            $formRecordData->addSingleSelectFieldRecordData($singleSelectFieldId, $selectedOptionId);
        }
        foreach ($this->request['multiSelectFieldRecords']  ?? [] as $multiSelectFieldRecord) {
            $multiSelectFieldId = $this->stripTagsVariable($multiSelectFieldRecord['fieldId']);
            $multiSelectFieldRecordData = new MultiSelectFieldRecordData($multiSelectFieldId);
            foreach ($multiSelectFieldRecord['selectedOptionIdList']  ?? [] as $selectedOptionId) {
                $multiSelectFieldRecordData->add($selectedOptionId);
            }
            $formRecordData->addMultiSelectFieldRecordData($multiSelectFieldRecordData);
        }
        return $formRecordData;
    }

}
