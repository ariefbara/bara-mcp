<?php

namespace Shared\Domain\Model;

use Shared\Domain\Model\FormRecord\ {
    AttachmentFieldRecord,
    IntegerFieldRecord,
    MultiSelectFieldRecord,
    SingleSelectFieldRecord,
    StringFieldRecord,
    TextAreaFieldRecord
};

interface HasFormRecordInterface
{

    function getSubmitTimeString(): string;

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
