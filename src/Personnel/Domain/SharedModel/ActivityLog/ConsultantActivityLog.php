<?php

namespace Personnel\Domain\SharedModel\ActivityLog;

use Personnel\Domain\{
    Model\Firm\Personnel\ProgramConsultant,
    SharedModel\ActivityLog
};

class ConsultantActivityLog
{

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ProgramConsultant
     */
    protected $consultant;

    public function __construct(ActivityLog $activityLog, string $id, ProgramConsultant $consultant)
    {
        $this->activityLog = $activityLog;
        $this->id = $id;
        $this->consultant = $consultant;
    }

}
