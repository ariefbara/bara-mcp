<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\Worksheet,
    SharedModel\ActivityLog,
    SharedModel\ContainActvityLog
};

class WorksheetActivityLog
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
    
    public function __construct(Worksheet $worksheet, string $id, string $message, ?TeamMembership $teamMember)
    {
        $this->worksheet = $worksheet;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $teamMember);
    }
}
