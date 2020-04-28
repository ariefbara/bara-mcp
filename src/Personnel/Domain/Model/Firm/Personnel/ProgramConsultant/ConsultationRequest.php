<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Program\ConsultationSetup,
    Program\Participant,
    Program\Participant\ConsultationSession
};
use Resources\Domain\ValueObject\DateTimeInterval;
use Shared\Domain\Model\ConsultationRequestStatusVO;

class ConsultationRequest
{

    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

    /**
     *
     * @var string
     */
    protected $id;
    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;


    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var ConsultationRequestStatusVO
     */
    protected $status;

    protected function __construct()
    {
        ;
    }
    
    public function offer()
    {
        
    }
    public function reject()
    {
        
    }
    
    public function accept()
    {
        
    }
    
    public function createConsultationSession(): ConsultationSession
    {
        
    }

}
