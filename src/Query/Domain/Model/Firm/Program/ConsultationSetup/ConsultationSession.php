<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultantFeedback;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ParticipantFeedback;
use Query\Domain\Model\Firm\Program\Participant;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;

class ConsultationSession
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
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;
    
    /**
     * 
     * @var ConsultationChannel
     */
    protected $channel;

    /**
     *
     * @var bool
     */
    protected $cancelled;
    
    /**
     * 
     * @var ConsultationSessionType
     */
    protected $sessionType;

    /**
     *
     * @var string|null
     */
    protected $note;

    /**
     *
     * @var ParticipantFeedback||null
     */
    protected $participantFeedback = null;

    /**
     *
     * @var ConsultantFeedback||null
     */
    protected $consultantFeedback = null;

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }

    function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    function getParticipantFeedback(): ?ParticipantFeedback
    {
        return $this->participantFeedback;
    }

    function getConsultantFeedback(): ?ConsultantFeedback
    {
        return $this->consultantFeedback;
    }

    function isCancelled(): bool
    {
        return $this->cancelled;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

    protected function __construct()
    {
        
    }

    function getStartTime(): string
    {
        return $this->startEndTime->getStartTime()->format("Y-m-d H:i:s");
    }

    function getEndTime(): string
    {
        return $this->startEndTime->getEndTime()->format("Y-m-d H:i:s");
    }
    
    public function getMedia(): ?string
    {
        return $this->channel->getMedia();
    }

    public function getAddress(): ?string
    {
        return $this->channel->getAddress();
    }
    
    public function getSessionTypeDisplayValue(): ?string
    {
        return $this->sessionType->getSessionTypeDisplayValue();
    }
    
    public function isApprovedByMentor(): ?bool
    {
        return $this->sessionType->isApprovedByMentor();
    }

    public function hasConsultantFeedback(): bool
    {
        return isset($this->consultantFeedback);
    }

    public function hasParticipantFeedback(): bool
    {
        return isset($this->participantFeedback);
    }

}
