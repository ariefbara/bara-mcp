<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    Event\ClientAcceptedConsultationRequest,
    Event\ClientChangedConsultationRequestTime,
    Event\ClientProposedConsultationRequest,
    Model\DependencyEntity\Firm\Client,
    Model\DependencyEntity\Firm\Program\Consultant\ConsultantComment,
    Model\DependencyEntity\Firm\Program\ConsultationSetup,
    Model\DependencyEntity\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment
};
use Query\Domain\Model\Firm\Program\Consultant;
use Resources\ {
    Domain\Model\ModelContainEvents,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecord;

class ClientParticipant extends ModelContainEvents
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
        $firmId = $this->client->getFirmId();
        $clientId = $this->client->getId();
        $programId = $this->program->getId();

        $event = new ClientProposedConsultationRequest($firmId, $clientId, $programId, $consultationRequestId);
        $this->recordEvent($event);

        return $this->participant->proposeConsultation(
                        $consultationRequestId, $consultationSetup, $consultant, $startTime);
    }

    public function reproposeConsultationRequest(string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $this->participant->reproposeConsultationRequest($consultationRequestId, $startTime);

        $firmId = $this->client->getFirmId();
        $clientId = $this->client->getId();
        $programId = $this->program->getId();
        $event = new ClientChangedConsultationRequestTime($firmId, $clientId, $programId, $consultationRequestId);

        $this->recordEvent($event);
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->participant->acceptConsultationRequest($consultationRequestId, $consultationSessionId);

        $firmId = $this->client->getFirmId();
        $clientId = $this->client->getId();
        $programId = $this->program->getId();
        $event = new ClientAcceptedConsultationRequest($firmId, $clientId, $programId, $consultationSessionId);

        $this->recordEvent($event);
    }

    public function createRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecord $formRecord): Worksheet
    {
        return $this->participant->createRootWorksheet($worksheetId, $name, $mission, $formRecord);
    }

    public function replyToComment(
            string $commentId, Comment $comment, string $message): Comment
    {
        if ($comment->isConsultantComment()) {
            $event = new \Participant\Domain\Event\Participant\Worksheet\ConsultantCommentRepliedByClientParticipant($message, $commentId, $programId, $worksheetId, $participantCommentId);
            $this->recordEvent($event);
        }
        return $comment->createReply($commentId, $message);
    }

}
