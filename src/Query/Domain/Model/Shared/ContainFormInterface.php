<?php

namespace Query\Domain\Model\Shared;

use Query\Domain\Model\Shared\Form\AttachmentField;
use Query\Domain\Model\Shared\Form\IntegerField;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use Query\Domain\Model\Shared\Form\Section;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use Query\Domain\Model\Shared\Form\StringField;
use Query\Domain\Model\Shared\Form\TextAreaField;

interface ContainFormInterface
{

    public function getName(): string;

    public function getDescription(): ?string;

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

    /**
     * 
     * @return Section[]
     */
    public function getUnremovedSections();
    
}
