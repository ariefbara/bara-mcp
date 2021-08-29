<?php

namespace Query\Domain\Model\Shared\Form;

interface IContainFieldRecord
{

    public function getIntegerFieldRecordValueCorrespondWith(IntegerField $integerField): ?int;

    public function getStringFieldRecordValueCorrespondWith(StringField $stringField): ?string;

    public function getTextAreaFieldRecordValueCorrespondWith(TextAreaField $textAreaField): ?string;

    public function getFileInfoListOfAttachmentFieldRecordCorrespondWith(AttachmentField $attachmentField): ?string;

    public function getSingleSelectFieldRecordSelectedOptionNameCorrespondWith(SingleSelectField $singleSelectField): ?string;

    public function getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith(MultiSelectField $multiSelectField): ?string;
}
