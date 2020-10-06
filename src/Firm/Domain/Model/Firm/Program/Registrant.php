<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Resources\Exception\RegularException;

class Registrant
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
    protected $registeredTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var string||null
     */
    protected $note;

    /**
     *
     * @var UserRegistrant|null
     */
    protected $userRegistrant;

    /**
     *
     * @var ClientRegistrant|null
     */
    protected $clientRegistrant;

    /**
     *
     * @var TeamRegistrant|null
     */
    protected $teamRegistrant;

    protected function __construct()
    {
        ;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function accept(): void
    {
        $this->assertUnconcluded();
        $this->concluded = true;
        $this->note = 'accepted';
    }

    public function reject(): void
    {
        $this->assertUnconcluded();
        $this->concluded = true;
        $this->note = 'rejected';
    }

    public function createParticipant(string $participantId): Participant
    {
        if (isset($this->userRegistrant)) {
            return $this->userRegistrant->createParticipant($this->program, $participantId);
        }
        if (isset($this->clientRegistrant)) {
            return $this->clientRegistrant->createParticipant($this->program, $participantId);
        }
        if (isset($this->teamRegistrant)) {
            return $this->teamRegistrant->createParticipant($this->program, $participantId);
        }
    }

    public function correspondWithUser(string $userId): bool
    {
        return empty($this->userRegistrant) ? false : $this->userRegistrant->userIdEquals($userId);
    }

    public function correspondWithClient(string $clientId): bool
    {
        return empty($this->clientRegistrant) ? false : $this->clientRegistrant->clientIdEquals($clientId);
    }

    public function correspondWithTeam(string $teamId): bool
    {
        return isset($this->teamRegistrant) ? $this->teamRegistrant->teamIdEquals($teamId) : false;
    }

    protected function assertUnconcluded(): void
    {
        if ($this->concluded) {
            $errorDetail = "forbidden: application already concluded";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
