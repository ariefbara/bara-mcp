<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\DependencyModel\Firm\Personnel\Coordinator;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class CoordinatorInvitee
{

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

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

}
