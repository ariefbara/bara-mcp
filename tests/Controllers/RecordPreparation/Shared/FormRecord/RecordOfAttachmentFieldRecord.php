<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\Form\RecordOfAttachmentField,
    Shared\RecordOfFormRecord
};

class RecordOfAttachmentFieldRecord implements Record
{
    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    /**
     *
     * @var RecordOfAttachmentField
     */
    public $attachmentField;
    public $id, $removed;
    
    function __construct(RecordOfFormRecord $formRecord, RecordOfAttachmentField $attachmentField, $index)
    {
        $this->formRecord = $formRecord;
        $this->attachmentField = $attachmentField;
        $this->id = "attachmentFieldRecord-$index-id";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "FormRecord_id" => $this->formRecord->id,
            "AttachmentField_id" => $this->attachmentField->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
