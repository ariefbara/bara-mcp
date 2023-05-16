<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport\KeyResultProgressReportAttachment;
use Resources\Uuid;

class KeyResultProgressReport
{

    /**
     * 
     * @var ObjectiveProgressReport
     */
    protected $objectiveProgressReport;

    /**
     * 
     * @var KeyResult
     */
    protected $keyResult;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var int|null
     */
    protected $value;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $attachments;

    public function __construct(
            ObjectiveProgressReport $objectiveProgressReport, KeyResult $keyResult, string $id,
            KeyResultProgressReportData $keyResultProgressReportData)
    {
        $this->objectiveProgressReport = $objectiveProgressReport;
        $this->keyResult = $keyResult;
        $this->id = $id;
        $this->value = $keyResultProgressReportData->getValue();
        $this->disabled = false;
        //
        $this->attachments = new ArrayCollection();
        foreach ($keyResultProgressReportData->getAttachments() as $fileInfo) {
            $this->attachments->add(new KeyResultProgressReportAttachment($this, $fileInfo, Uuid::generateUuid4()));
        }
    }

    public function update(KeyResultProgressReportData $keyResultProgressReportData): void
    {
        $this->value = $keyResultProgressReportData->getValue();
        $this->disabled = false;
        //
        foreach ($keyResultProgressReportData->getAttachments() as $fileInfo) {
            $p = fn(KeyResultProgressReportAttachment $attachment) => $attachment->fileInfoEquals($fileInfo);
            $existingAttachment = $this->attachments->filter($p)->first();
            if ($existingAttachment) {
                $existingAttachment->renew();
            } else {
                $this->attachments->add(new KeyResultProgressReportAttachment($this, $fileInfo, Uuid::generateUuid4()));
            }
        }
        foreach ($this->attachments->getIterator() as $attachment) {
            $attachment->removeIfIrrelevant();
        }
    }

    public function disableIfCorrespondWithInactiveKeyResult(): void
    {
        if (!$this->keyResult->isActive()) {
            $this->disabled = true;
        }
    }

    public function correspondWith(KeyResult $keyResult): bool
    {
        return $this->keyResult === $keyResult;
    }

}
