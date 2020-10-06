<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet;

use Query\Domain\ {
    Model\Firm\Program\Participant\Worksheet,
    SharedModel\ActivityLog
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

    public function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getMessage(): string
    {
        return $this->activityLog->getMessage();
    }

    public function getOccuredTimeString(): string
    {
        return $this->activityLog->getOccuredTimeString();
    }

}
