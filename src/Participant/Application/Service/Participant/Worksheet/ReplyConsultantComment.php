<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Application\Service\ClientParticipantRepository;
use Resources\Application\Event\Dispatcher;

class ReplyConsultantComment
{

    /**
     *
     * @var ParticipantCommentRepository
     */
    protected $participantCommentRepository;

    /**
     *
     * @var ConsultantCommentRepository
     */
    protected $consultantCommentRepository;

    /**
     *
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(
            ParticipantCommentRepository $participantCommentRepository,
            ConsultantCommentRepository $consultantCommentRepository,
            ClientParticipantRepository $clientParticipantRepository, Dispatcher $dispatcher)
    {
        $this->participantCommentRepository = $participantCommentRepository;
        $this->consultantCommentRepository = $consultantCommentRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $programId, string $worksheetId,
            string $consultantCommentId, string $message): string
    {
        $id = $this->participantCommentRepository->nextIdentity();

        $consultantComment = $this->consultantCommentRepository->aConsultantCommentOfClientParticipant(
                $firmId, $clientId, $programId, $worksheetId, $consultantCommentId);
        $clientParticipat = $this->clientParticipantRepository->ofId($firmId, $clientId, $programId);
        $participantComment = $clientParticipat->replyToConsultantComment($id, $consultantComment, $message);

        $this->participantCommentRepository->add($participantComment);
        
        $this->dispatcher->dispatch($clientParticipat);
        
        return $id;
    }

}
