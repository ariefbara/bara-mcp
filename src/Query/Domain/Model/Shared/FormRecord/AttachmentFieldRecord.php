<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Shared\ {
    Form\AttachmentField,
    FormRecord,
    FormRecord\AttachmentFieldRecord\AttachedFile
};

class AttachmentFieldRecord
{

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var AttachmentField
     */
    protected $attachmentField;

    /**
     *
     * @var ArrayCollection
     */
    protected $attachedFiles;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getAttachmentField(): AttachmentField
    {
        return $this->attachmentField;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    /**
     * 
     * @return AttachedFile[]
     */
    function getUnremovedAttachedFiles()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        return $this->attachedFiles->matching($criteria)->getIterator();
    }

    protected function __construct()
    {
        ;
    }

}
