<?php

namespace ActivityInvitee\Domain\Model\Invitee;

use ActivityInvitee\Domain\Model\Invitee;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};

class InviteeReport
{

    /**
     *
     * @var Invitee
     */
    protected $invitee;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    function __construct(Invitee $invitee, string $id, FormRecord $formRecord)
    {
        $this->invitee = $invitee;
        $this->id = $id;
        $this->formRecord = $formRecord;
    }
    
    public function update(FormRecordData $formRecordData): void
    {
        $this->formRecord->update($formRecordData);
    }

}
