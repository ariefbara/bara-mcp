<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Client;

use ActivityInvitee\Domain\ {
    DependencyModel\Firm\Client,
    Model\ParticipantInvitee
};
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class TeamMembership
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $teamId;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }
    
    public function submitInviteeReportIn(ParticipantInvitee $activityInvitation, FormRecordData $formRecordData): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$activityInvitation->belongsToTeam($this->teamId)) {
            $errorDetail = "forbidden: only allowed to submit report in invitation belongs to team";
            throw RegularException::forbidden($errorDetail);
        }
        $activityInvitation->submitReport($formRecordData);
    }

}
