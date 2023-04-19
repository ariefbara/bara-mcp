<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use SharedContext\Domain\Model\SharedEntity\FileInfo;

class KeyResultProgressReportData
{

    /**
     * 
     * @var int|null
     */
    protected $value;

    /**
     * 
     * @var FileInfo[]
     */
    protected $attachments = [];
    protected $fileInfoIdListOfAttachment = [];

    public function addFileInfoIdAsAttachment(string $fileInfoId)
    {
        $this->fileInfoIdListOfAttachment[] = $fileInfoId;
        return $this;
    }

    public function getFileInfoIdListOfAttachment()
    {
        return $this->fileInfoIdListOfAttachment;
    }

    public function __construct(?int $value)
    {
        $this->value = $value;
    }

    public function addAttachment(FileInfo $attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    //
    public function getValue(): ?int
    {
        return $this->value;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

}
