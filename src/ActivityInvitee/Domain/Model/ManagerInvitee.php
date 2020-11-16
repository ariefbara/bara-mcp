<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\DependencyModel\Firm\Manager;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ManagerInvitee
{

    /**
     *
     * @var Manager
     */
    protected $manager;

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
