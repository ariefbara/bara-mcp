<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team\Member;

use Tests\Controllers\RecordPreparation\{
    Firm\Team\RecordOfMember,
    Record,
    Shared\RecordOfActivityLog
};

class RecordOfTeamMemberActivityLog implements Record
{

    /**
     *
     * @var RecordOfMember
     */
    public $member;

    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    public $id;

    public function __construct(RecordOfMember $member, RecordOfActivityLog $activityLog)
    {
        $this->member = $member;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "TeamMember_id" => $this->member->id,
            "id" => $this->id,
            "ActivityLog_id" => $this->activityLog->id,
        ];
    }

}
