<?php

namespace Notification\Domain\Model\Firm\Program\Participant\Worksheet;

use Notification\Domain\Model\Firm\Program\ {
    Consultant\ConsultantComment,
    Participant\Worksheet
};
use Resources\ {
    Application\Service\Mailer,
    Domain\Model\Mail,
    Domain\Model\Mail\Recipient,
    Exception\RegularException
};

class Comment
{
    
    /**
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     *
     * @var Comment||null
     */
    protected $parent;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    /**
     *
     * @var ConsultantComment||null
     */
    protected $consultantComment = null;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getConsultantWriterMailRecipient(): Recipient
    {
        if (empty($this->consultantComment)) {
            $errorDetail = 'forbidden: unable to retrieve consultant mail info';
            throw RegularException::forbidden($errorDetail);
        }
        return $this->consultantComment->getConsultantMailRecipient();
    }
    
    public function sendMailToConsultantWhoseCommentBeingReplied(Mailer $mailer): void
    {
        if (empty($this->parent)) {
            $errorDetail = 'forbidden: empty parent comment';
            throw RegularException::forbidden($errorDetail);
        }
        $recipient = $this->parent->getConsultantWriterMailRecipient();
        $firmWhitelableInfo = $this->worksheet->getFirmWhitelableInfo();
        
        $subject = "konsulta: komen balasan dari peserta program";
        $body = <<<_BODY
Hi konsultan {$recipient->getFirstName()},

Komen kamu di worksheet milik peserta {$this->worksheet->getParticipantName()} telah di balas.
Untuk melanjutkan komunikasi, kujungi:
{$firmWhitelableInfo->getUrl()}/personnel/as-program-consultants/{$this->worksheet->getProgramId()}/participants/{$this->worksheet->getParticipantId()}/worksheets/{$this->worksheet->getId()}/comments/{$this->id}
_BODY;
        $alternativeBody = null;
        
        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        
        $senderName = $firmWhitelableInfo->getMailSenderName();
        $senderAddress = $firmWhitelableInfo->getMailSenderAddress();
        
        $mailer->send($mail, $senderName, $senderAddress);
    }

}
