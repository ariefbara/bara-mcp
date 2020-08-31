<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Resources\{
    DateTimeImmutableBuilder,
    Domain\Model\Mail\Recipient,
    Exception\RegularException
};

class Participant
{

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

    public function isActive(): bool
    {
        return $this->active;
    }

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->enrolledTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->active = true;
        $this->note = null;
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

    public function getMailRecipient(): Recipient
    {
        if (!empty($this->clientParticipant)) {
            return  $this->clientParticipant->getClientMailRecipient();
        }
        return $this->userParticipant->getUserMailRecipient();
    }

    public function getParticipantName(): string
    {
        if (!empty($this->clientParticipant)) {
            return  $this->clientParticipant->getClientName();
        }
        return $this->userParticipant->getUserName();
    }

}
