<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Firm\Program,
    User
};
use Resources\ {
    Application\Service\Mailer,
    Domain\Model\Mail,
    Domain\Model\Mail\Recipient
};

class UserParticipant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var Participant
     */
    protected $participant;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Program $program, string $id, User $user)
    {
        $this->program = $program;
        $this->id = $id;
        $this->user = $user;
        $this->participant = new Participant($id);
    }

    public function reenroll(): void
    {
        $this->participant->reenroll();
    }

    public function bootout(): void
    {
        $this->participant->bootout();
    }

    public function correspondWithRegistrant(UserRegistrant $userRegistrant): bool
    {
        return $userRegistrant->userEquals($this->user);
    }
    
    public function sendRegistrationAcceptedMail(Mailer $mailer): void
    {
        $whiteLableInfo = $this->program->getFirmWhitelableInfo();
        
        $subject = "Konsulta - {$this->program->getFirmName()}: program registration accepted";
        
        $body = <<<_MAILBODY
Hi {$this->user->getPersonName()->getFirstName()},

Selamat, kamu telah diterima sebagai peserta program {$this->program->getName()}.

Sekarang kamu bisa mulai perjalanan kamu di program dengan mengunjungi:

{$whiteLableInfo->getUrl()}/user/program-participations/{$this->program->getFirmId()}/{$this->program->getId()}
_MAILBODY;

        $alternativeBody = null;
        $recipient = $this->getUserMailRecipient();
        
        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        
        $senderName = $whiteLableInfo->getMailSenderName();
        $senderAddress = $whiteLableInfo->getMailSenderAddress();
        
        $mailer->send($mail, $senderName, $senderAddress);
    }
    
    public function getUserMailRecipient(): Recipient
    {
        return $this->user->getMailRecipient();
    }
    
    public function getUserName(): string
    {
        return $this->user->getName();
    }

}
