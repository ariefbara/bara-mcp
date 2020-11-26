<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\{
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    Model\Invitee\InviteeReport
};
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class Invitee
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipant
     */
    protected $activityParticipant;

    /**
     *
     * @var bool|null
     */
    protected $willAttend;

    /**
     *
     * @var bool|null
     */
    protected $attended;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var InviteeReport|null
     */
    protected $report;

    protected function __construct()
    {
        
    }

    public function submitReport(FormRecordData $formRecordData): void
    {
        if (isset($this->report)) {
            $this->report->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->activityParticipant->createFormRecord($id, $formRecordData);
            $this->report = new InviteeReport($this, $id, $formRecord);
        }
    }

}
