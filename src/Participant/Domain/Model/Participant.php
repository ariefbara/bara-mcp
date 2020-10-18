<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Participant\Domain\ {
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\CompletedMission,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\ViewLearningMaterialActivityLog,
    Model\Participant\Worksheet
};
use Resources\ {
    Domain\Model\EntityContainEvents,
    Exception\RegularException,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class Participant extends EntityContainEvents implements AssetBelongsToTeamInterface
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
     * @var ArrayCollection
     */
    protected $consultationRequests;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    /**
     *
     * @var TeamProgramParticipation|null
     */
    protected $teamProgramParticipation;

    /**
     *
     * @var ArrayCollection
     */
    protected $completedMissions;

    protected function __construct()
    {
        
    }

    public function belongsToTeam(Team $team): bool
    {
        return isset($this->teamProgramParticipation) ? $this->teamProgramParticipation->belongsToTeam($team) : false;
    }

    public function quit(): void
    {
        if (!$this->active) {
            $errorDetail = 'forbidden: participant already inactive';
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
        $this->note = 'quit';
    }

    public function submitConsultationRequest(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime, ?TeamMembership $teamMember = null): ConsultationRequest
    {
        if (!$consultationSetup->programEquals($this->program)) {
            $errorDetail = 'forbidden: consultation setup from different program';
            throw RegularException::forbidden($errorDetail);
        }

        if (!$consultant->programEquals($this->program)) {
            $errorDetail = 'forbidden: consultant from different program';
            throw RegularException::forbidden($errorDetail);
        }
        $consultationRequest = new ConsultationRequest(
                $this, $consultationRequestId, $consultationSetup, $consultant, $startTime, $teamMember);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        return $consultationRequest;
    }

    public function changeConsultationRequestTime(
            string $consultationRequestId, DateTimeImmutable $startTime, ?TeamMembership $teamMember = null): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);
        $consultationRequest->rePropose($startTime, $teamMember);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        $this->recordedEvents = $consultationRequest->pullRecordedEvents();
    }

    public function acceptOfferedConsultationRequest(
            string $consultationRequestId, string $consultationSessionId, ?TeamMembership $teamMember = null): void
    {
        $consultationRequest = $this->getConsultationRequestOrDie($consultationRequestId);

        $this->assertNoProposedConsultationRequestInCollectionConflictedWith($consultationRequest);
        $this->assertNoConsultationSessioninCollectionConflictedWithConsultationRequest($consultationRequest);

        $consultationRequest->accept();

        $consultationSession = $consultationRequest->createConsultationSession($consultationSessionId, $teamMember);
        $this->consultationSessions->add($consultationSession);

        $this->recordedEvents = $consultationSession->pullRecordedEvents();
    }

    public function createRootWorksheet(string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData, ?TeamMembership $teamMember = null): Worksheet
    {
        $this->assertActive();
        if (!$mission->programEquals($this->program)) {
            $errorDetail = "forbidden: can only access mission in same program";
            throw RegularException::forbidden($errorDetail);
        }

        $this->addCompletedMission($mission);

        return Worksheet::createRootWorksheet($this, $worksheetId, $name, $mission, $formRecordData, $teamMember);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $branchId, string $name, Mission $mission,
            FormRecordData $formRecordData, ?TeamMembership $teamMember = null): Worksheet
    {
        if (!$parentWorksheet->belongsToParticipant($this)) {
            $errorDetail = "forbidden: can manage asset belongs to other participant";
            throw RegularException::forbidden($errorDetail);
        }

        $this->addCompletedMission($mission);

        return $parentWorksheet->createBranchWorksheet($branchId, $name, $mission, $formRecordData, $teamMember);
    }

    protected function addCompletedMission(Mission $mission): void
    {

        $p = function (CompletedMission $completedMission) use ($mission) {
            return $completedMission->correspondWithMission($mission);
        };
        if (empty($this->completedMissions->filter($p)->count())) {
            $id = Uuid::generateUuid4();
            $completedMission = new CompletedMission($this, $id, $mission);
            $this->completedMissions->add($completedMission);
        }
    }

    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

    public function logViewLearningMaterialActivity(
            string $LogId, string $learningMaterialId, ?TeamMembership $teamMember = null): ViewLearningMaterialActivityLog
    {
        return new ViewLearningMaterialActivityLog($this, $LogId, $learningMaterialId, $teamMember);
    }

    protected function getConsultationRequestOrDie(string $consultationRequestId): ConsultationRequest
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $consultationRequestId));
        $consultationRequest = $this->consultationRequests->matching($criteria)->first();
        if (empty($consultationRequest)) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
        return $consultationRequest;
    }

    protected function assertNoProposedConsultationRequestInCollectionConflictedWith(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationRequest $otherConsultationRequest) use ($consultationRequest) {
            return $otherConsultationRequest->isProposedConsultationRequestConflictedWith($consultationRequest);
        };
        if (!empty($this->consultationRequests->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation request waiting for consultant response";
            throw RegularException::conflict($errorDetail);
        }
    }

    protected function assertNoConsultationSessioninCollectionConflictedWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->conflictedWithConsultationRequest($consultationRequest);
        };
        if (!empty($this->consultationSessions->filter($p)->count())) {
            $errorDetail = "conflict: requested time already occupied by your other consultation session";
            throw RegularException::conflict($errorDetail);
        }
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active program participant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
