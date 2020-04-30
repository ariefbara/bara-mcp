<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\{
    Client,
    Firm\Personnel\ProgramConsultant\ConsultationRequest,
    Firm\Personnel\ProgramConsultant\ConsultationSession,
    Firm\Program
};

class Participant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $acceptedTime;

    /**
     *
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string
     */
    protected $note;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getClient(): Client
    {
        return $this->client;
    }

    function getAcceptedTimeString(): string
    {
        return $this->acceptedTime->format("Y-m-d H:i:s");
    }

    function isActive(): bool
    {
        return $this->active;
    }

    function getNote(): string
    {
        return $this->note;
    }

    protected function __construct()
    {
        ;
    }

    public function hasConsultationSessionInConflictWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->intersectWithConsultationRequest($consultationRequest);
        };
        return !empty($this->consultationSessions->filter($p)->count());
    }

}
