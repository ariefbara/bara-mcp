<?php

namespace App\Http\Controllers\Client\ProgramParticipation\Worksheet;

use App\Http\Controllers\{
    Client\ClientBaseController,
    SwiftMailerBuilder
};
use Config\EventList;
use Notification\{
    Application\Listener\Firm\Program\ClientParticipant\Worksheet\ConsultantCommentRepliedByClientParticipantListener,
    Application\Service\Firm\Program\ClientParticipant\Worksheet\SendParticipantRepliedConsultantCommentMail,
    Domain\Model\Firm\Program\Participant\Worksheet\ParticipantComment as ParticipantComment2
};
use Participant\{
    Application\Service\Participant\Worksheet\ReplyConsultantComment,
    Application\Service\Participant\Worksheet\SubmitNewComment,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet,
    Domain\Model\Participant\Worksheet\ParticipantComment
};
use Query\{
    Application\Service\Firm\Client\ProgramParticipation\Worksheet\ViewComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment,
    Domain\Model\Firm\Program\Participant\Worksheet\ConsultantComment
};
use Resources\Application\Event\Dispatcher;

class CommentController extends ClientBaseController
{

    public function submitNew($programId, $worksheetId)
    {
        $service = $this->buildSubmitNewService();
        $message = $this->stripTagsInputRequest('message');
        $commentId = $service->execute($this->firmId(), $this->clientId(), $programId, $worksheetId, $message);
        
        $viewService = $this->buildViewService();
        $comment = $viewService->showById($this->firmId(), $this->clientId(), $programId, $worksheetId, $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }
    
    protected function replyConsultantComment($programId, $worksheetId, $consultantCommentId)
    {
        $service = $this->buildReplyConsultantCommentService();
        $message = $this->stripTagsInputRequest('message');
        $commentId = $service->execute($this->firmId(), $this->clientId(), $programId, $worksheetId, $consultantCommentId, $message);
        
        $viewService = $this->buildViewService();
        $comment = $viewService->showById($this->firmId(), $this->clientId(), $programId, $worksheetId, $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }

    public function show($programId, $worksheetId, $commentId)
    {
        $viewService = $this->buildViewService();
        $comment = $viewService->showById($this->firmId(), $this->clientId(), $programId, $worksheetId, $commentId);
        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }

    public function showAll($programId, $worksheetId)
    {
        $viewService = $this->buildViewService();
        $comments = $viewService->showAll($this->firmId(), $this->clientId(), $programId, $worksheetId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($comments);
        foreach ($comments as $comment) {
            $result['list'][] = $this->arrayDataOfComment($comment);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfComment(Comment $comment): array
    {
        return [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            'removed' => $comment->isRemoved(),
            "parent" => $this->arrayDataOfParentComment($comment->getParent()),
            "consultant" => $this->arrayDataOfConsultant($comment->getConsultantComment()),
        ];
    }

    protected function arrayDataOfParentComment(?Comment $comment): ?array
    {
        return empty($comment) ? null : [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            "removed" => $comment->isRemoved(),
        ];
    }

    protected function arrayDataOfConsultant(?ConsultantComment $consultantComment): ?array
    {
        return empty($consultantComment) ? null : [
            'id' => $consultantComment->getConsultant()->getId(),
            'personnel' => [
                'id' => $consultantComment->getConsultant()->getPersonnel()->getId(),
                'name' => $consultantComment->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }

    protected function buildSubmitNewService()
    {
        $participantCommentRepository = $this->em->getRepository(ParticipantComment::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new SubmitNewComment($participantCommentRepository, $worksheetRepository);
    }

    protected function buildReplyConsultantCommentService()
    {
        $participantCommentRepository = $this->em->getRepository(ParticipantComment::class);
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::CONSULTANT_COMMENT_REPLIED_BY_CLIENT_PARTICIPANT,
                $this->buildConsultantCommentRepliedByParticipantListener());

        return new ReplyConsultantComment(
                $participantCommentRepository, $consultantCommentRepository, $clientParticipantRepository, $dispatcher);
    }

    protected function buildViewService()
    {
        $commentRepository = $this->em->getRepository(Comment::class);
        return new ViewComment($commentRepository);
    }

    protected function buildConsultantCommentRepliedByParticipantListener()
    {
        $participantCommentRepository = $this->em->getRepository(ParticipantComment2::class);
        $mailer = SwiftMailerBuilder::build();

        $sendParticipantRepliedConsultantCommentMail = new SendParticipantRepliedConsultantCommentMail($participantCommentRepository,
                $mailer);
        return new ConsultantCommentRepliedByClientParticipantListener($sendParticipantRepliedConsultantCommentMail);
    }

}
