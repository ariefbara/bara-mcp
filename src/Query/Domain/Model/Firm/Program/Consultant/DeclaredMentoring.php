<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\ValueObject\Schedule;

class DeclaredMentoring
{

    /**
     * 
     * @var Consultant
     */
    protected $mentor;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DeclaredMentoringStatus
     */
    protected $status;

    /**
     * 
     * @var Schedule
     */
    protected $schedule;

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var Mentoring
     */
    protected $mentoring;

}
