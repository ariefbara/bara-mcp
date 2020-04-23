<?php

namespace Firm\Domain\Model\Shared;

use Firm\Domain\Model\Shared\Form\ {
    AttachmentFieldData,
    IntegerFieldData,
    MultiSelectFieldData,
    SingleSelectFieldData,
    StringFieldData,
    TextAreaFieldData
};
use Resources\Domain\Data\DataCollection;

class FormData
{

    protected $name, $description;

    /**
     *
     * @var DataCollection
     */
    protected $stringFieldDataCollection;

    /**
     *
     * @var DataCollection
     */
    protected $integerFieldDataCollection;

    /**
     *
     * @var DataCollection
     */
    protected $textAreaFieldDataCollection;

    /**
     *
     * @var DataCollection
     */
    protected $attachmentFieldDataCollection;

    /**
     *
     * @var DataCollection
     */
    protected $singleSelectFieldDataCollection;

    /**
     *
     * @var DataCollection
     */
    protected $multiSelectFieldDataCollection;

    function getName()
    {
        return $this->name;
    }

    function getDescription()
    {
        return $this->description;
    }

    function getStringFieldDataCollection(): DataCollection
    {
        return $this->stringFieldDataCollection;
    }

    function getIntegerFieldDataCollection(): DataCollection
    {
        return $this->integerFieldDataCollection;
    }

    function getTextAreaFieldDataCollection(): DataCollection
    {
        return $this->textAreaFieldDataCollection;
    }

    function getAttachmentFieldDataCollection(): DataCollection
    {
        return $this->attachmentFieldDataCollection;
    }

    function getSingleSelectFieldDataCollection(): DataCollection
    {
        return $this->singleSelectFieldDataCollection;
    }

    function getMultiSelectFieldDataCollection(): DataCollection
    {
        return $this->multiSelectFieldDataCollection;
    }

    function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;

        $this->stringFieldDataCollection = new DataCollection();
        $this->integerFieldDataCollection = new DataCollection();
        $this->textAreaFieldDataCollection = new DataCollection();
        $this->singleSelectFieldDataCollection = new DataCollection();
        $this->multiSelectFieldDataCollection = new DataCollection();
        $this->attachmentFieldDataCollection = new DataCollection();
    }

    public function pushStringFieldData(StringFieldData $stringFieldData, ?string $stringFieldId): void
    {
        $this->stringFieldDataCollection->push($stringFieldData, $stringFieldId);
    }
    public function pullStringFieldDataOfId(string $stringFieldId): ?StringFieldData
    {
        return $this->stringFieldDataCollection->pull($stringFieldId);
    }

    public function pushIntegerFieldData(IntegerFieldData $integerFieldData, ?string $integerFieldId): void
    {
        $this->integerFieldDataCollection->push($integerFieldData, $integerFieldId);
    }
    public function pullIntegerFieldDataOfId(string $integerFieldId): ?IntegerFieldData
    {
        return $this->integerFieldDataCollection->pull($integerFieldId);
    }

    public function pushTextAreaFieldData(TextAreaFieldData $textAreaFieldData, ?string $textAreaFieldId): void
    {
        $this->textAreaFieldDataCollection->push($textAreaFieldData, $textAreaFieldId);
    }
    public function pullTextAreaFieldDataOfId(string $textAreaFieldId): ?TextAreaFieldData
    {
        return $this->textAreaFieldDataCollection->pull($textAreaFieldId);
    }

    public function pushSingleSelectFieldData(SingleSelectFieldData $singleSelectFieldData, ?string $singleSelectFieldId): void
    {
        $this->singleSelectFieldDataCollection->push($singleSelectFieldData, $singleSelectFieldId);
    }
    public function pullSingleSelectFieldDataOfId(string $singleSelectFieldId): ?SingleSelectFieldData
    {
        return $this->singleSelectFieldDataCollection->pull($singleSelectFieldId);
    }

    public function pushMultiSelectFieldData(MultiSelectFieldData $multiSelectFieldData, ?string $multiSelectFieldId): void
    {
        $this->multiSelectFieldDataCollection->push($multiSelectFieldData, $multiSelectFieldId);
    }
    public function pullMultiSelectFieldDataOfId(string $multiSelectFieldId): ?MultiSelectFieldData
    {
        return $this->multiSelectFieldDataCollection->pull($multiSelectFieldId);
    }

    public function pushAttachmentFieldData(AttachmentFieldData $attachmentFieldData, ?string $attachmentFieldId): void
    {
        $this->attachmentFieldDataCollection->push($attachmentFieldData, $attachmentFieldId);
    }
    public function pullAttachmentFieldDataOfId(string $attachmentFieldId): ?AttachmentFieldData
    {
        return $this->attachmentFieldDataCollection->pull($attachmentFieldId);
    }

}
