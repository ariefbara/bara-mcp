<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    Event\ClientParticipantAcceptedConsultationRequest,
    Event\ClientParticipantChangedConsultationRequestTime,
    Event\ClientParticipantProposedConsultationRequest,
    Event\Participant\Worksheet\ConsultantCommentRepliedByClientParticipant,
    Model\DependencyEntity\Firm\Client,
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
        
        $event = new ClientParticipantProposedConsultationRequest($firmId, $clientId, $this->id, $consultationRequestId);
        $this->recordEvent($event);

        return $this->participant->submitConsultationRequest(
                        $consultationRequestId, $consultationSetup, $consultant, $startTime);
    }

    public function reproposeConsultationRequest(string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $this->participant->changeConsultationRequestTime($consultationRequestId, $startTime);
        
        $firmId = $this->client->getFirmId();
        $clientId = $this->client->getId();
        
        $event = new ClientParticipantChangedConsultationRequestTime($firmId, $clientId, $this->id, $consultationRequestId);
        $this->recordEvent($event);
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->participant->acceptOfferedConsultationRequest($consultationRequestId, $consultationSessionId);
        
        $firmId = $this->client->getFirmId();
        $clientId = $this->client->getId();
        
        $event = new ClientParticipantAcceptedConsultationRequest($firmId, $clientId, $this->id, $consultationSessionId);
        $this->recordEvent($event);
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
            $firmId = $this->client->getFirmId();
            $clientId = $this->client->getId();
            $worksheetId = $comment->getWorksheetId();
            
            $event = new ConsultantCommentRepliedByClientParticipant(
                    $firmId, $clientId, $this->id, $worksheetId, $commentId);
            $this->recordEvent($event);
        }
        return $comment->createReply($commentId, $message);
    }

}
