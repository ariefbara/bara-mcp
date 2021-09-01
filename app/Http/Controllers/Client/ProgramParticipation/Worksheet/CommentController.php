<?php

namespace App\Http\Controllers\Client\ProgramParticipation\Worksheet;

use App\Http\Controllers\Client\ClientBaseController;
use Config\EventList;
use Notification\{
    Application\Listener\ConsultantCommentRepliedByParticipantListener,
    Application\Service\GenerateNotificationWhenConsultantCommentRepliedByParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment as Comment3
};
use Participant\{
    Application\Service\ClientParticipant\Worksheet\ReplyComment,
    Application\Service\ClientParticipant\Worksheet\SubmitNewComment,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet,
    Domain\Model\Participant\Worksheet\Comment as Comment2
};
use Query\{
    Application\Service\Firm\Client\ProgramParticipation\Worksheet\ViewComment,
    Domain\Model\Firm\Program\Consultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Application\Event\Dispatcher;

class CommentController extends ClientBaseController
{

    public function submitNew($programParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitNewService();
        $message = $this->stripTagsInputRequest('message');
        $commentId = $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $worksheetId,
                $message);

        $viewService = $this->buildViewService();
        $comment = $viewService->showById(
                $this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }

    public function reply($programParticipationId, $worksheetId, $commentId)
    {

        $service = $this->buildReplyService();
        $message = $this->stripTagsInputRequest('message');
        $replyCommentId = $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $commentId, $message);

        $viewService = $this->buildViewService();
        $reply = $viewService->showById(
                $this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $replyCommentId);
        
        $response = $this->commandCreatedResponse($this->arrayDataOfComment($reply));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
    }

    public function show($programParticipationId, $worksheetId, $commentId)
    {
        $viewService = $this->buildViewService();
        $comment = $viewService->showById(
                $this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $commentId);
        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }

    public function showAll($programParticipationId, $worksheetId)
    {
        $viewService = $this->buildViewService();
        $comments = $viewService->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $this->getPage(),
                $this->getPageSize());

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
            "consultantComment" => $this->arrayDataOfConsultantComment($comment->getConsultantComment()),
        ];
    }

    protected function arrayDataOfParentComment(?Comment $comment): ?array
    {
        return empty($comment) ? null : [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            "removed" => $comment->isRemoved(),
            "consultantComment" => $this->arrayDataOfConsultantComment($comment->getConsultantComment()),
        ];
    }

    protected function arrayDataOfConsultantComment(?ConsultantComment $consultantComment): ?array
    {
        return empty($consultantComment) ? null : [
            'id' => $consultantComment->getId(),
            'consultant' => [
                'id' => $consultantComment->getConsultant()->getId(),
                'personnel' => [
                    'id' => $consultantComment->getConsultant()->getPersonnel()->getId(),
                    'name' => $consultantComment->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }

    protected function buildSubmitNewService()
    {
        $commentRepository = $this->em->getRepository(Comment2::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);

        return new SubmitNewComment($commentRepository, $worksheetRepository);
    }

    protected function buildReplyService()
    {
        $commentRepository = $this->em->getRepository(Comment2::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::COMMENT_FROM_CONSULTANT_REPLIED, $this->buildConsultantCommentRepliedByParticipantListener());

        return new ReplyComment($commentRepository, $clientParticipantRepository, $dispatcher);
    }
    protected function buildConsultantCommentRepliedByParticipantListener()
    {
        $commentRepository = $this->em->getRepository(Comment3::class);
        $service = new GenerateNotificationWhenConsultantCommentRepliedByParticipant($commentRepository);
        return new ConsultantCommentRepliedByParticipantListener($service);
    }

    protected function buildViewService()
    {
        $commentRepository = $this->em->getRepository(Comment::class);
        return new ViewComment($commentRepository);
    }

}
