<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\DependencyModel\Firm\Personnel\Consultant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ConsultantInvitee
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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
