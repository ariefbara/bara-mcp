<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Task;

use Personnel\Domain\Model\Firm\Program\Participant\Task;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;

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
     * @var TaskReportReviewStatus
     */
    protected $reviewStatus;

    protected function __construct()
    {
        
    }

    public function approve(): void
    {
        $this->reviewStatus = $this->reviewStatus->approve();
    }

    public function askForRevision(): void
    {
        $this->reviewStatus = $this->reviewStatus->askForRevision();
    }

}
