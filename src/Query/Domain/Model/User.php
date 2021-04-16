<?php

namespace Query\Domain\Model;

use DateTimeImmutable;
use Query\Application\Service\User\ConsultationSessionRepository;
use Query\Application\Service\User\ParticipantInviteeRepository;
use Query\Domain\Service\DataFinder;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use Resources\Exception\RegularException;

class User
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var PersonName
     */
    protected $personName;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var Password
     */
    protected $password;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $signupTime;

    /**
     *
     * @var string
     */
    protected $activationCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $activationCodeExpiredTime = null;

    /**
     *
     * @var string
     */
    protected $resetPasswordCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $resetPasswordCodeExpiredTime = null;

    /**
     *
     * @var bool
     */
    protected $activated = false;

    function getId(): string
    {
        return $this->id;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function getSignupTimeString(): string
    {
        return $this->signupTime->format('Y-m-d H:i:s');
    }

    function isActivated(): bool
    {
        return $this->activated;
    }

    protected function assertActive()
    {
        if (!$this->activated) {
            throw RegularException::forbidden('forbidden: only active user can make this request');
        }
    }

    protected function __construct()
    {
        
    }

    public function passwordMatches(string $password): bool
    {
        return $this->password->match($password);
    }

    public function getFullName(): string
    {
        return $this->personName->getFullName();
    }

    public function getFirstName(): string
    {
        return $this->personName->getFirstName();
    }

    public function getLastName(): string
    {
        return $this->personName->getLastName();
    }

    public function viewAllActiveProgramParticipationSummary(DataFinder $dataFinder, int $page, int $pageSize): array
    {
        return $dataFinder->summaryOfAllUserProgramParticipations($this->id, $page, $pageSize);
    }

    public function viewAllActivityInvitations(
            ParticipantInviteeRepository $participantInviteeRepository, int $page, int $pageSize,
            ?InviteeFilter $inviteeFilter)
    {
        $this->assertActive();
        return $participantInviteeRepository
                        ->allParticipantInviteeBelongsToUser($this->id, $page, $pageSize, $inviteeFilter);
    }

    public function viewAllConsultationSessions(
            ConsultationSessionRepository $consultationSessionRepository, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $this->assertActive();
        return $consultationSessionRepository
                ->allConsultationSessionBelongsToUser($this->id, $page, $pageSize, $consultationSessionFilter);
    }

}
