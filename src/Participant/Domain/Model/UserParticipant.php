<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    Event\Participant\Worksheet\ConsultantCommentRepliedByUserParticipant,
    Event\UserParticipantAcceptedConsultationRequest,
    Event\UserParticipantChangedConsultationRequestTime,
    Event\UserParticipantProposedConsultationRequest,
    Model\DependencyEntity\Firm\Program\Consultant,
    Model\DependencyEntity\Firm\Program\ConsultationSetup,
    Model\DependencyEntity\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment
};
use Resources\ {
    Domain\Model\ModelContainEvents,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecord;

class UserParticipant extends ModelContainEvents
{

    /**
     *
     * @var string
     */
    protected $userId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        ;
    }

    public function quit(): void
    {
        $this->participant->quit();
    }

    public function proposeConsultation(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime): ConsultationRequest
    {
        $event = new UserParticipantProposedConsultationRequest($this->userId, $this->id, $consultationRequestId);
        $this->recordEvent($event);
        
        return $this->participant->proposeConsultation($consultationRequestId, $consultationSetup, $consultant, $startTime);
    }
    
    public function reproposeConsultationRequest(string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $event = new UserParticipantChangedConsultationRequestTime($this->userId, $this->id, $consultationRequestId);
        $this->recordEvent($event);
        
        $this->participant->reproposeConsultationRequest($consultationRequestId, $startTime);
    }
    
    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        
        $event = new UserParticipantAcceptedConsultationRequest($this->userId, $this->id, $consultationSessionId);
        $this->recordEvent($event);
        
        $this->participant->acceptConsultationRequest($consultationRequestId, $consultationSessionId);
    }
    
    public function createRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecord $formRecord): Worksheet
    {
        return $this->participant->createRootWorksheet($worksheetId, $name, $mission, $formRecord);
    }
    
    public function replyComment(
            string $commentId, Comment $comment, string $message): Comment
    {
        if ($comment->isConsultantComment()) {
            $worksheetId = $comment->getWorksheetId();
            $event = new ConsultantCommentRepliedByUserParticipant(
                    $this->userId, $this->id, $worksheetId, $commentId);
            $this->recordEvent($event);
        }
        return $comment->createReply($commentId, $message);
    }

}
