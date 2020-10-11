<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\{
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    Event\ClientParticipantAcceptedConsultationRequest,
    Event\ClientParticipantChangedConsultationRequestTime,
    Event\ClientParticipantProposedConsultationRequest,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment
};
use Resources\{
    Domain\Model\EntityContainEvents,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ClientParticipant extends EntityContainEvents
{

    /**
     *
     * @var Client
     */
    protected $client;

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
        return $this->participant->submitConsultationRequest(
                        $consultationRequestId, $consultationSetup, $consultant, $startTime);
    }

    public function reproposeConsultationRequest(string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $this->participant->changeConsultationRequestTime($consultationRequestId, $startTime);
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->participant->acceptOfferedConsultationRequest($consultationRequestId, $consultationSessionId);
    }

    public function createRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        return $this->participant->createRootWorksheet($worksheetId, $name, $mission, $formRecordData, $teamMember = null);
    }

    public function replyComment(
            string $commentId, Comment $comment, string $message): Comment
    {
        return $comment->createReply($commentId, $message, $teamMember = null);
    }

}
