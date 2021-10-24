<?php

namespace Participant\Domain\Task\Participant;

use DateTimeImmutable;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;

class DeclareConsultationSessionPayload
{

    /**
     * 
     * @var string|null
     */
    protected $consultationSetupId;

    /**
     * 
     * @var string|null
     */
    protected $mentorId;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $startTime;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $endTime;

    /**
     * 
     * @var string|null
     */
    protected $media;

    /**
     * 
     * @var string|null
     */
    protected $address;

    public function getConsultationSetupId(): ?string
    {
        return $this->consultationSetupId;
    }

    public function getMentorId(): ?string
    {
        return $this->mentorId;
    }

    public function __construct(
            ?string $consultationSetupId, ?string $mentorId, ?DateTimeImmutable $startTime, ?DateTimeImmutable $endTime,
            ?string $media, ?string $address)
    {
        $this->consultationSetupId = $consultationSetupId;
        $this->mentorId = $mentorId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->media = $media;
        $this->address = $address;
    }
    
    public function getStartEndTime(): DateTimeInterval
    {
        return new DateTimeInterval($this->startTime, $this->endTime);
    }
    
    public function getConsultationChannel(): ConsultationChannel
    {
        return new ConsultationChannel($this->media, $this->address);
    }

}
