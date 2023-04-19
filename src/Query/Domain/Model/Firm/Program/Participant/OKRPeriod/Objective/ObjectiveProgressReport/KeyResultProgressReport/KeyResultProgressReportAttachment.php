<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use Query\Domain\Model\Shared\FileInfo;

class KeyResultProgressReportAttachment
{

    /**
     * 
     * @var KeyResultProgressReport
     */
    protected $keyResultProgressReport;

    /**
     * 
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }

    public function getKeyResultProgressReport(): KeyResultProgressReport
    {
        return $this->keyResultProgressReport;
    }

    public function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

}
