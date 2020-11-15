<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\DependencyModel\Firm\Program\Participant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ParticipantInvitee
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Invitee
     */
    protected $invitee;
    
    protected function __construct()
    {
        
    }

    public function submitReport(FormRecordData $formRecordData): void
    {
        $this->invitee->submitReport($formRecordData);
    }
    
    public function belongsToTeam(string $teamId): bool
    {
        return $this->participant->belongsToTeam($teamId);
    }

}
