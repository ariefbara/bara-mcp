<?php

namespace Query\Domain\Model\Firm\Program\Participant\Task;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm\Program\Participant\Task;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport\TaskReportAttachment;

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

    protected function __construct()
    {
        
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedTime(): DateTimeImmutable
    {
        return $this->createdTime;
    }

    public function getModifiedTime(): DateTimeImmutable
    {
        return $this->modifiedTime;
    }

    /**
     * 
     * @return TaskReportAttachment[]
     */
    public function iterateActiveAttachments()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('removed', false));
        return $this->attachments->matching($criteria)->getIterator();
    }

}
