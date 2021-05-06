<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Application\Service\Firm\Program\Participant\MetricAssignment\MetricAssignmentReportRepository;
use Query\Application\Service\Personnel\ConsultationRequestRepository;
use Query\Application\Service\Personnel\ConsultationSessionRepository;
use Query\Application\Service\Personnel\InviteeRepository;
use Query\Application\Service\Personnel\RegistrantRepository;
use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use Resources\Exception\RegularException;

class Personnel
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
    protected $name;

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
     * @var string|null
     */
    protected $phone;

    /**
     *
     * @var string|null
     */
    protected $bio;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    /**
     *
     * @var string|null
     */
    protected $resetPasswordCode;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $resetPasswordCodeExpiredTime;

    /**
     *
     * @var bool
     */
    protected $active;

    /**
     *
     * @var ArrayCollection
     */
    protected $programCoordinators;

    /**
     *
     * @var ArrayCollection
     */
    protected $programConsultants;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    function getJoinTimeString(): string
    {
        return $this->joinTime->format('Y-m-d H:i:s');
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    /**
     * 
     * @return Coordinator[]
     */
    function getUnremovedProgramCoordinators()
    {
        return $this->programCoordinators->matching($this->activeCriteria())->getIterator();
    }

    /**
     * 
     * @return Consultant[]
     */
    function getUnremovedProgramConsultants()
    {
        return $this->programConsultants->matching($this->activeCriteria())->getIterator();
    }

    protected function assertActive()
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active personnel can make this request');
        }
    }

    protected function __construct()
    {
        ;
    }

    public function passwordMatches(string $password): bool
    {
        return $this->password->match($password);
    }

    private function activeCriteria()
    {
        return Criteria::create()
                        ->andWhere(Criteria::expr()->eq('active', true));
    }

    public function getName(): string
    {
        return $this->name->getFullName();
    }

    public function getFirstName(): string
    {
        return $this->name->getFirstName();
    }

    public function getLastName(): ?string
    {
        return $this->name->getLastName();
    }

    public function viewAllConsultationSessions(
            ConsultationSessionRepository $consultationSessionRepository, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $this->assertActive();
        return $consultationSessionRepository->allConsultationSessionBelongsToPersonnel(
                        $this->id, $page, $pageSize, $consultationSessionFilter);
    }

    public function viewAllConsultationRequests(
            ConsultationRequestRepository $consultationRequestRepository, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $this->assertActive();
        return $consultationRequestRepository->allConsultationRequestBelongsToPersonnel(
                        $this->id, $page, $pageSize, $consultationRequestFilter);
    }

    public function viewAllAccessibleRegistrant(
            RegistrantRepository $registrantRepository, int $page, int $pageSize, ?bool $concludedStatus)
    {
        $this->assertActive();
        return $registrantRepository->allRegistrantsAccessibleByPersonnel($this->id, $page, $pageSize, $concludedStatus);
    }
    
    public function viewAllActivityInvitation(
            InviteeRepository $inviteeRepository, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        $this->assertActive();
        return $inviteeRepository->allActivityInvitationsToPersonnel($this->id, $page, $pageSize, $inviteeFilter);
    }
    
    public function viewAllAccesibleMetricAssignmentReports(
            MetricAssignmentReportRepository $metricAssignmentReportRepository, int $page, int $pageSize, 
            ?bool $approvedStatus)
    {
        $this->assertActive();
        return $metricAssignmentReportRepository
                ->allMetricAssignmentReportsAccessibleByPersonnel($this->id, $page, $pageSize, $approvedStatus);
    }

}
