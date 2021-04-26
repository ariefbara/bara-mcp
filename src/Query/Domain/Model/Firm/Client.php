<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Query\Application\Service\Client\ConsultationRequestRepository;
use Query\Application\Service\Client\ConsultationSessionRepository;
use Query\Application\Service\Client\ParticipantInviteeRepository;
use Query\Domain\Model\Firm;
use Query\Domain\Service\DataFinder;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use Resources\Exception\RegularException;

class Client
{

    /**
     *
     * @var Firm
     */
    protected $firm;

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

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSignupTimeString(): ?string
    {
        return isset($this->signupTime) ? $this->signupTime->format("Y-m-d H:i:s") : null;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    protected function __construct()
    {
        ;
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

    public function passwordMatch(string $password): bool
    {
        return $this->password->match($password);
    }

    protected function assertActive(): void
    {
        if (!$this->activated) {
            throw RegularException::forbidden('forbidden: only active client can make this request');
        }
    }

    public function viewAllActiveProgramParticipationSummary(DataFinder $dataFinder, int $page, int $pageSize): array
    {
        $this->assertActive();
        return $dataFinder->summaryOfAllClientProgramParticipations($this->id, $page, $pageSize);
    }

    public function viewAllAccessibleConsultationSessions(
            ConsultationSessionRepository $consultationSessionRepository, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $this->assertActive();
        return $consultationSessionRepository->allAccessibleConsultationSesssionBelongsToClient(
                $this->id, $page, $pageSize, $consultationSessionFilter);
    }

    public function viewAllAccessibleConsultationRequest(
            ConsultationRequestRepository $consultationRequestRepository, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $this->assertActive();
        return $consultationRequestRepository->allAccessibleConsultationSesssionBelongsToClient(
                $this->id, $page, $pageSize, $consultationRequestFilter);
    }
    
    public function viewAllAccessibleActivityInvitations(
            ParticipantInviteeRepository $participantInviteeRepository, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        $this->assertActive();
        return $participantInviteeRepository
                ->allAccessibleParticipantInviteeBelongsToClient($this->id, $page, $pageSize, $inviteeFilter);
    }

}
