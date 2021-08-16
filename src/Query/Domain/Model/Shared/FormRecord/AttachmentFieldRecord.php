<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Shared\Form\AttachmentField;
use Query\Domain\Model\Shared\Form\TextAreaField;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord\AttachedFile;

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
    }
    
    public function isActiveFieldRecordCorrespondWith(AttachmentField $attachmentField): bool
    {
        return !$this->removed && $this->attachmentField === $attachmentField;
    }
    
    public function getStringOfAttachedFileLocationList(): ?string
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        
        $result = null;
        foreach ($this->attachedFiles->matching($criteria)->getIterator() as $attachedFile) {
            $result .= empty($result) ? $attachedFile->getFileLocation() : "\r\n{$attachedFile->getFileLocation()}";
        }
        return $result;
    }

}
