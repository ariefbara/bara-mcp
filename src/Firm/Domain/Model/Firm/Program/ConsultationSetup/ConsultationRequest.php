<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\ConsultationSetup;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\Domain\ValueObject\DateTimeInterval;

class ConsultationRequest
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
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string||null
     */
    protected $status;

    protected function __construct()
    {
        
    }

    public function disableUpcomingRequest(): void
    {
        if (!$this->concluded && $this->startEndTime->isUpcoming()) {
            $this->concluded = true;
            $this->status = "disabled by system";
        }
    }

}
