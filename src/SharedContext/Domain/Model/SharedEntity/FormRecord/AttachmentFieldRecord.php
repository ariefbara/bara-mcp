<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\ {
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

    function getAttachmentField(): AttachmentField
    {
        return $this->attachmentField;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function __construct(
            FormRecord $formRecord, string $id, AttachmentField $attachmentField, array $fileInfoList)
    {
        $this->formRecord = $formRecord;
        $this->id = $id;
        $this->attachmentField = $attachmentField;
        $this->removed = false;

        $this->attachedFiles = new ArrayCollection();
        foreach ($fileInfoList as $fileInfo) {
            $id = Uuid::generateUuid4();
            $this->attachedFiles->add(new AttachedFile($this, $id, $fileInfo));
        }
    }

    public function setAttachedFiles(array $fileInfoList): void
    {
        foreach ($fileInfoList as $fileInfo) {
            $criteria = Criteria::create()
                    ->andWhere(Criteria::expr()->eq('fileInfo', $fileInfo))
                    ->andWhere(Criteria::expr()->eq('removed', false));
            if (empty($this->attachedFiles->matching($criteria)->count())) {
                $id = Uuid::generateUuid4();
                $this->attachedFiles->add(new AttachedFile($this, $id, $fileInfo));
            }
        }
        $p = function (AttachedFile $attachedFile) use ($fileInfoList) {
            return $attachedFile->isUnremovedAttachmentOfFileNotIncludedIn($fileInfoList);
        };
        foreach ($this->attachedFiles->filter($p)->getIterator() as $attachedFile) {
            $attachedFile->remove();
        }
    }

    public function isReferToRemovedField(): bool
    {
        return $this->attachmentField->isRemoved();
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
