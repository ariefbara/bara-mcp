<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\{
    Consultant,
    ConsultationSetup
};
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationSession
{

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var string|null
     */
    protected $note;

    protected function __construct()
    {
        
    }

    public function disableUpcomingSession(): void
    {
        if (!$this->cancelled && $this->startEndTime->isUpcoming()) {
            $this->cancelled = true;
            $this->note = "inactive consultant";
        }
    }

}
