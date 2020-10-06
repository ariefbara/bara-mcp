<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\Worksheet,
    SharedModel\ActivityLog,
    SharedModel\ContainActvityLog
};

class WorksheetActivityLog implements ContainActvityLog
{
    /**
     *
     * @var Worksheet
     */
    protected $worksheet;
    /**
     *
     * @var string
     */
    protected $id;
    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;
    
    public function __construct(Worksheet $worksheet, string $id, string $message)
    {
        $this->worksheet = $worksheet;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message);
    }

    
    public function setOperator(TeamMembership $teamMember): void
    {
        $this->activityLog->setOperator($teamMember);
    }

}
