<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program\ActivityType;

use ActivityInvitee\Domain\DependencyModel\Firm\FeedbackForm;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};

class ActivityParticipant
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FeedbackForm|null
     */
    protected $reportForm;

    protected function __construct()
    {
        
    }
    
    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return $this->reportForm->createFormRecord($formRecordId, $formRecordData);
    }

}
