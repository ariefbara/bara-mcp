<?php

namespace Notification\Application\Service\Firm\Program\Participant\Worksheet;

use Resources\Application\Service\Mailer;

class SendClientParticipantRepliedConsultantCommentMail
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var Mailer
     */
    protected $mailer;

    public function __construct(CommentRepository $commentRepository, Mailer $mailer)
    {
        $this->commentRepository = $commentRepository;
        $this->mailer = $mailer;
    }

    public function execute(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $commentId): void
    {
        $this->commentRepository
                ->aCommentInClientParticipantWorksheet(
                        $firmId, $clientId, $programParticipationId, $worksheetId, $commentId)
                ->sendMailToConsultantWhoseCommentBeingReplied($this->mailer);
    }

}
