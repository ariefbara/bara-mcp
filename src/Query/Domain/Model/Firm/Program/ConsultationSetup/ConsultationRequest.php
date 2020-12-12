<?php

namespace Query\Domain\Model\Firm\Program\ConsultationSetup;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\ConsultationSetup;
use Query\Domain\Model\Firm\Program\Participant;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;

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
    protected $concluded;

    /**
     *
     * @var string||null
     */
    protected $status;

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

    function isConcluded(): bool
    {
        return $this->concluded;
    }

    function getStatus(): ?string
    {
        return $this->status;
    }

    protected function __construct()
    {
        ;
    }

    function getStartTimeString(): string
    {
        return $this->startEndTime->getStartTime()->format('Y-m-d H:i:s');
    }

    function getEndTimeString(): string
    {
        return $this->startEndTime->getEndTime()->format('Y-m-d H:i:s');
    }

    public function getMedia(): ?string
    {
        return $this->channel->getMedia();
    }

    public function getAddress(): ?string
    {
        return $this->channel->getAddress();
    }

}
