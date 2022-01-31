<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Personnel\Domain\Model\Firm\Personnel;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ContainSchedule;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DeclaredMentoring;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Worksheet;
use Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;
use SharedContext\Domain\ValueObject\ScheduleData;

class ProgramConsultant extends EntityContainEvents
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var bool
     */
    protected $active;

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
     * @var ArrayCollection
     */
    protected $mentoringRequests;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $mentoringSlots;

    protected function __construct()
    {
        
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $this->assertActive();

        $consultationRequest = $this->findConsultationRequestOrDie($consultationRequestId);

        $this->assertNoConsultationSessionInConflictWithConsultationRequest($consultationRequest);
        $this->assertNoOtherOfferedConsultationRequestInConflictWithConsultationRequest(
                $consultationRequest);

        $consultationRequest->accept();
        $consultationSessionId = Uuid::generateUuid4();
        $consultationSession = $consultationRequest->createConsultationSession($consultationSessionId);
        $this->consultationSessions->add($consultationSession);

        $firmId = $this->personnel->getFirmId();
        $personnelId = $this->personnel->getId();

        $this->aggregateEventFrom($consultationSession);
    }

    public function offerConsultationRequestTime(
            string $consultationRequestId, ConsultationRequestData $consultationRequestData): void
    {
        $this->assertActive();

        $consultationRequest = $this->findConsultationRequestOrDie($consultationRequestId);
        $consultationRequest->offer($consultationRequestData);

        $this->assertNoConsultationSessionInConflictWithConsultationRequest($consultationRequest);
        $this->assertNoOtherOfferedConsultationRequestInConflictWithConsultationRequest(
                $consultationRequest);

        $firmId = $this->personnel->getFirmId();
        $personnelId = $this->personnel->getId();

        $this->aggregateEventFrom($consultationRequest);
    }

    public function submitNewCommentOnWorksheet(
            string $consultantCommentId, Worksheet $worksheet, string $message): ConsultantComment
    {
        $this->assertActive();
        $this->assertAssetBelongsToParticipantInSameProgram($worksheet);

        $comment = new Comment($worksheet, $consultantCommentId, $message);
        return new ConsultantComment($this, $consultantCommentId, $comment);
    }

    public function submitReplyOnWorksheetComment(string $consultantCommentId, Comment $comment, string $message): ConsultantComment
    {
        $this->assertActive();
        $this->assertAssetBelongsToParticipantInSameProgram($comment);

        $reply = $comment->createReply($consultantCommentId, $message);
        return new ConsultantComment($this, $consultantCommentId, $reply);
    }

    protected function assertNoConsultationSessionInConflictWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->intersectWithConsultationRequest($consultationRequest);
        };
        if (!empty($this->consultationSessions->filter($p)->count())) {
            $errorDetail = "forbidden: you already have consultation session at designated time";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNoOtherOfferedConsultationRequestInConflictWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('concluded', false));
        $p = function (ConsultationRequest $otherConsultationRequest) use ($consultationRequest) {
            return $otherConsultationRequest->isOfferedConsultationRequestConflictedWith($consultationRequest);
        };
        if (!empty($this->consultationRequests->matching($criteria)->filter($p)->count())) {
            $errorDetail = 'forbidden: you already offer designated time in other consultation request';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function findConsultationRequestOrDie(string $consultationRequestId): ConsultationRequest
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $consultationRequestId));
        $consultationRequest = $this->consultationRequests->matching($criteria)->first();
        if (empty($consultationRequest)) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
        return $consultationRequest;
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active consultant can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertAssetBelongsToParticipantInSameProgram(AssetBelongsToParticipantInProgram $asset): void
    {
        if (!$asset->belongsToParticipantInProgram($this->programId)) {
            $errorDetail = "forbidden: can only manage asset related to your program";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function verifyAssetUsable(IUsableInProgram $asset): void
    {
        $asset->assertUsableInProgram($this->programId);
    }

    public function executeTask(ITaskExecutableByMentor $task): void
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active mentor can make this request');
        }
        $task->execute($this);
    }

    public function declareConsultationSession(
            string $consultationSessionId, Participant $participant, ConsultationSetup $consultationSetup,
            DateTimeInterval $startEndTime, ConsultationChannel $channel): ConsultationSession
    {
        if (!$consultationSetup->usableInProgram($this->programId)) {
            throw RegularException::forbidden('forbidden: unuseable consultation setup');
        }
        if (!$participant->manageableInProgram($this->programId)) {
            throw RegularException::forbidden('forbidden: unamanged participant');
        }
        $type = new ConsultationSessionType(ConsultationSessionType::DECLARED_TYPE, true);
        return new ConsultationSession(
                $this, $consultationSessionId, $participant, $consultationSetup, $startEndTime, $channel, $type);
    }

    public function createMentoringSlot(
            string $mentoringSlotId, ConsultationSetup $consultationSetup, MentoringSlotData $mentoringSlotData): MentoringSlot
    {
        if (!$consultationSetup->usableInProgram($this->programId)) {
            throw RegularException::forbidden('forbidden: unuseable consultation setup');
        }
        return new MentoringSlot($this, $mentoringSlotId, $consultationSetup, $mentoringSlotData);
    }
    
    public function assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule(ContainSchedule $containSchedule): void
    {
        $p = function(MentoringRequest $mentoringRequest) use($containSchedule) {
            return $mentoringRequest->isScheduledOrOfferedRequestInConflictWith($containSchedule);
        };
        if (!empty($this->mentoringRequests->filter($p)->count())) {
            throw RegularException::forbidden('forbidden: schedule in conflict with scheduled or proposed request');
        }
        
        $mentoringSlotFilter = function(MentoringSlot $mentoringSlot) use($containSchedule) {
            return $mentoringSlot->isActiveSlotInConflictWith($containSchedule);
        };
        if (!empty($this->mentoringSlots->filter($mentoringSlotFilter)->count())) {
            throw RegularException::forbidden('forbidden: schedule in conflict with existing slot');
        }
    }
    
    public function declareMentoring(
            string $declaredMentoringId, Participant $participant, ConsultationSetup $consultationSetup, 
            ScheduleData $scheduleData): DeclaredMentoring
    {
        $this->assertActive();
        $participant->assertUsableInProgram($this->programId);
        $consultationSetup->assertUsableInProgram($this->programId);
        return new DeclaredMentoring($this, $declaredMentoringId, $participant, $consultationSetup, $scheduleData);
    }

}
