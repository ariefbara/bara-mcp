<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantProfileFilter;

class ViewAllParticipantProfilePayload
{

    /**
     * 
     * @var ParticipantProfileFilter
     */
    protected $participantProfileFilter;
    public $result;

    public function getParticipantProfileFilter(): ParticipantProfileFilter
    {
        return $this->participantProfileFilter;
    }

    public function __construct(ParticipantProfileFilter $participantProfileFilter)
    {
        $this->participantProfileFilter = $participantProfileFilter;
    }

}
