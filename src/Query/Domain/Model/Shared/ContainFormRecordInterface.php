<?php

namespace Query\Domain\Model\Shared;

use Query\Domain\Model\Shared\FormRecord\ {
    AttachmentFieldRecord,
    IntegerFieldRecord,
    MultiSelectFieldRecord,
    SingleSelectFieldRecord,
    StringFieldRecord,
    TextAreaFieldRecord
};

interface ContainFormRecordInterface
{

    function getSubmitTimeString(): ?string;

    /**
     * 
     * @return IntegerFieldRecord[]
     */
    function getUnremovedIntegerFieldRecords();

    /**
     * 
     * @return StringFieldRecord[]
     */
    function getUnremovedStringFieldRecords();

    /**
     * 
     * @return TextAreaFieldRecord[]
     */
    function getUnremovedTextAreaFieldRecords();

    /**
     * 
     * @return SingleSelectFieldRecord[]
     */
    function getUnremovedSingleSelectFieldRecords();

    /**
     * 
     * @return MultiSelectFieldRecord[]
     */
    function getUnremovedMultiSelectFieldRecords();

    /**
     * 
     * @return AttachmentFieldRecord[]
     */
    function getUnremovedAttachmentFieldRecords();
}
