<?php

namespace Personnel\Domain\Task\Mentor;

use DateTimeImmutable;

class DeclareConsultationSessionPayload
{

    /**
     * 
     * @var string|null
     */
    protected $consultationSetupeId;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

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

    public function getConsultationSetupeId(): ?string
    {
        return $this->consultationSetupeId;
    }

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function __construct(
            ?string $consultationSetupeId, ?string $participantId, ?DateTimeImmutable $startTime,
            ?DateTimeImmutable $endTime, ?string $media, ?string $address)
    {
        $this->consultationSetupeId = $consultationSetupeId;
        $this->participantId = $participantId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->media = $media;
        $this->address = $address;
    }

}
