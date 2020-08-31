<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Client,
    Program
};
use Resources\ {
    Application\Service\Mailer,
    Domain\Model\Mail,
    Domain\Model\Mail\Recipient
};

class ClientParticipant
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
     * @var Client
     */
    protected $client;

    /**
     *
     * @var Participant
     */
    protected $participant;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Program $program, string $id, Client $client)
    {
        $this->program = $program;
        $this->id = $id;
        $this->client = $client;
        $this->participant = new Participant($id);
    }

    public function bootout(): void
    {
        $this->participant->bootout();
    }

    public function reenroll(): void
    {
        $this->participant->reenroll();
    }

    public function correspondWithRegistrant(ClientRegistrant $clientRegistrant): bool
    {
        return $clientRegistrant->clientEquals($this->client);
    }
    
    public function sendRegistrationAcceptedMail(Mailer $mailer): void
    {
        $whiteLableInfo = $this->program->getFirmWhitelableInfo();
        
        $subject = "Konsulta - {$this->program->getFirmName()}: program registration accepted";
        
        $body = <<<_MAILBODY
Hi {$this->client->getPersonName()->getFirstName()},

Selamat, kamu telah diterima sebagai peserta program {$this->program->getName()}.

Sekarang kamu bisa mulai perjalanan kamu di program dengan mengunjungi:

{$whiteLableInfo->getUrl()}/client/program-participations/{$this->program->getId()}
_MAILBODY;

        $alternativeBody = null;
        $recipient = $this->getClientMailRecipient();
        
        $mail = new Mail($subject, $body, $alternativeBody, $recipient);
        
        $senderName = $whiteLableInfo->getMailSenderName();
        $senderAddress = $whiteLableInfo->getMailSenderAddress();
        
        $mailer->send($mail, $senderName, $senderAddress);
    }
    
    public function getClientMailRecipient(): Recipient
    {
        return $this->client->getMailRecipient();
    }
    
    public function getClientName(): string
    {
        return $this->client->getName();
    }

}
