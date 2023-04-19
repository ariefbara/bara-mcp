<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

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

    /**
     * 
     * @var bool
     */
    protected $relevantAttachment;

    public function __construct(KeyResultProgressReport $keyResultProgressReport, FileInfo $fileInfo, string $id)
    {
        $this->keyResultProgressReport = $keyResultProgressReport;
        $this->fileInfo = $fileInfo;
        $this->id = $id;
        $this->removed = false;
//        //
        $this->relevantAttachment = true;
    }

    public function renew(): void
    {
        $this->removed = false;
        $this->relevantAttachment = true;
    }

    public function removeIfIrrelevant(): void
    {
        if (!$this->relevantAttachment) {
            $this->removed = true;
        }
    }

    //
    public function fileInfoEquals(FileInfo $fileInfo): bool
    {
        return $this->fileInfo === $fileInfo;
    }

}
