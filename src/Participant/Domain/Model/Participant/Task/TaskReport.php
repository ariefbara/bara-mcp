<?php

namespace Participant\Domain\Model\Participant\Task;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Participant\Domain\Model\Participant\Task;
use Participant\Domain\Model\Participant\Task\TaskReport\TaskReportAttachment;
use Resources\DateTimeImmutableBuilder;
use Resources\Uuid;

class TaskReport
{

    /**
     * 
     * @var Task
     */
    protected $task;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $content;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $createdTime;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var ArrayCollection
     */
    protected $attachments;

    protected function attachFiles(TaskReportData $data): void
    {
        foreach ($data->getAttachedParticipantFileInfoList() as $participantFileInfo) {
            $taskReportAttachment = new TaskReportAttachment($this, Uuid::generateUuid4(), $participantFileInfo);
            $this->attachments->add($taskReportAttachment);
        }
    }

    public function __construct(Task $task, string $id, TaskReportData $data)
    {
        $this->task = $task;
        $this->id = $id;
        $this->content = $data->getContent();
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->attachments = new ArrayCollection();
        //
        $this->attachFiles($data);
    }

    public function update(TaskReportData $data): void
    {
        $previousContent = $this->content;
        $existingAttachmentUpdated = false;
        $previousAttachmentCount = $this->attachments->count();

        $this->content = $data->getContent();

        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));

        foreach ($this->attachments->matching($criteria)->getIterator() as $attachment) {
            $isUpdated = $attachment->update($data);
            $existingAttachmentUpdated = $existingAttachmentUpdated || $isUpdated;
        }

        $this->attachFiles($data);

        if ($previousContent != $this->content || $existingAttachmentUpdated || $previousAttachmentCount !== $this->attachments->count()) {
            $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
    }

}
