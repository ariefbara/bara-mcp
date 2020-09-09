<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Resources\{
    DateTimeImmutableBuilder,
    Exception\RegularException
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
     * @var DateTimeImmutable
     */
    protected $enrolledTime;

    /**
     *
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string||null
     */
    protected $note;

    /**
     *
     * @var ClientParticipant||null
     */
    protected $clientParticipant;

    /**
     *
     * @var UserParticipant||null
     */
    protected $userParticipant;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Program $program, string $id)
    {
        $this->program = $program;
        $this->id = $id;
        $this->enrolledTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->active = true;
        $this->note = null;
    }

    public static function participantForUser(Program $program, string $id, string $userId): self
    {
        $participant = new static($program, $id);
        $participant->userParticipant = new UserParticipant($participant, $id, $userId);
        return $participant;
    }

    public static function participantForClient(Program $program, string $id, string $clientId): self
    {
        $participant = new static($program, $id);
        $participant->clientParticipant = new ClientParticipant($participant, $id, $clientId);
        return $participant;
    }

    public function bootout(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: participant already inactive';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        $this->note = 'booted';
    }

    public function reenroll(): void
    {
        if ($this->active) {
            $errorDetail = 'forbidden: already active participant';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = true;
        $this->note = null;
    }

    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return !empty($this->clientParticipant) ?
                $this->clientParticipant->correspondWithRegistrant($registrant) :
                $this->userParticipant->correspondWithRegistrant($registrant);
    }

}
