<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\ {
    Firm\Team\TeamProgramParticipation,
    Program\Consultant,
    Program\Participant
};

class ConsultationRequest
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
     * @var Consultant
     */
    protected $consultant;
    
    protected function __construct()
    {
        ;
    }
    
    public function sendParticipantProposedConsultationRequestMail(): void
    {
        $this->participant->registerMailRecipients($this);
    }
    
    public function addMail()
    {
        
    }

}
