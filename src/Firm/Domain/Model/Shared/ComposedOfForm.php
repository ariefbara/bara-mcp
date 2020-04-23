<?php

namespace Firm\Domain\Model\Shared;

use Firm\Domain\Model\Shared\Form\ {
    AttachmentField,
    IntegerField,
    MultiSelectField,
    SingleSelectField,
    StringField,
    TextAreaField
};

interface ComposedOfForm
{

    public function getName(): string;


    public function getDescription(): string;

    /**
     * 
     * @return StringField[]
     */
    public function getUnremovedStringFields();

    /**
     * 
     * @return IntegerField[]
     */
    public function getUnremovedIntegerFields();

    /**
     * 
     * @return TextAreaField[]
     */
    public function getUnremovedTextAreaFields();

    /**
     * 
     * @return AttachmentField[]
     */
    public function getUnremovedAttachmentFields();

    /**
     * 
     * @return SingleSelectField[]
     */
    public function getUnremovedSingleSelectFields();

    /**
     * 
     * @return MultiSelectField[]
     */
    public function getUnremovedMultiSelectFields();
}
