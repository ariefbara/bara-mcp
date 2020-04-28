<?php

namespace Shared\Domain\Model;

use Shared\Domain\Model\FormRecordData\ {
    AttachmentFieldRecordData,
    MultiSelectFieldRecordData
};

class FormRecordData
{

    /**
     *
     * @var array
     */
    protected $stringFieldRecordDatas = [];

    /**
     *
     * @var array
     */
    protected $integerFieldRecordDatas = [];

    /**
     *
     * @var array
     */
    protected $textAreaFieldRecordDatas = [];

    /**
     *
     * @var AttachmentFieldRecordData[]
     */
    protected $attachmentFieldRecordDatas = [];

    /**
     *
     * @var array
     */
    protected $singleSelectFieldRecordDatas = [];

    /**
     *
     * @var MultiSelectFieldRecordData[]
     */
    protected $multiSelectFieldRecordDatas = [];

    function __construct()
    {
        $this->stringFieldRecordDatas = [];
        $this->integerFieldRecordDatas = [];
        $this->textAreaFieldRecordDatas = [];
        $this->attachmentFieldRecordDatas = [];
        $this->singleSelectFieldRecordDatas = [];
        $this->multiSelectFieldRecordDatas = [];
    }

    public function addStringFieldRecordData(string $stringFieldId, ?string $input): void
    {
        $this->stringFieldRecordDatas[$stringFieldId] = $input;
    }

    public function addIntegerFieldRecordData(string $integerFieldId, ?int $input): void
    {
        $this->integerFieldRecordDatas[$integerFieldId] = $input;
    }

    public function addTextAreaFieldRecordData(string $textAreaFieldId, ?string $input): void
    {
        $this->textAreaFieldRecordDatas[$textAreaFieldId] = $input;
    }

    public function addAttachmentFieldRecordData(AttachmentFieldRecordData $attachmentFieldRecordData): void
    {
        $attachmentFieldId = $attachmentFieldRecordData->getAttachmentFieldId();
        $this->attachmentFieldRecordDatas[$attachmentFieldId] = $attachmentFieldRecordData;
    }

    public function addSingleSelectFieldRecordData(string $singleSelectFieldId, ?string $selectedOptionId): void
    {
        $this->singleSelectFieldRecordDatas[$singleSelectFieldId] = $selectedOptionId;
    }

    public function addMultiSelectFieldRecordData(MultiSelectFieldRecordData $multiSelectFieldRecordData): void
    {
        $multiSelectFieldId = $multiSelectFieldRecordData->getMultiSelectFieldId();
        $this->multiSelectFieldRecordDatas[$multiSelectFieldId] = $multiSelectFieldRecordData;
    }

    public function getStringFieldRecordDataOf(string $stringFieldId)
    {
        return (isset($this->stringFieldRecordDatas[$stringFieldId])) ?
                $this->stringFieldRecordDatas[$stringFieldId] : null;
    }

    public function getIntegerFieldRecordDataOf(string $integerFieldId)
    {
        return (isset($this->integerFieldRecordDatas[$integerFieldId])) ?
                $this->integerFieldRecordDatas[$integerFieldId] : null;
    }

    public function getTextAreaFieldRecordDataOf(string $textAreaFieldId)
    {
        return (isset($this->textAreaFieldRecordDatas[$textAreaFieldId])) ?
                $this->textAreaFieldRecordDatas[$textAreaFieldId] : null;
    }

    public function getAttachedFileInfoListOf(string $attachmentFieldId): array
    {
        return (isset($this->attachmentFieldRecordDatas[$attachmentFieldId])) ?
                $this->attachmentFieldRecordDatas[$attachmentFieldId]->getFileInfoCollection(): [];
    }

    public function getSelectedOptionIdOf(string $singleSelectFieldId)
    {
        return (isset($this->singleSelectFieldRecordDatas[$singleSelectFieldId])) ?
                $this->singleSelectFieldRecordDatas[$singleSelectFieldId] : null;
    }

    public function getSelectedOptionIdListOf(string $multiSelectFieldId): array
    {
        return (isset($this->multiSelectFieldRecordDatas[$multiSelectFieldId])) ?
                $this->multiSelectFieldRecordDatas[$multiSelectFieldId]->getSelectedOptionIds(): [];
    }

}
