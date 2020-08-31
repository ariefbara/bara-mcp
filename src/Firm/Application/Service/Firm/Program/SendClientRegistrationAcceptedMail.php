<?php

namespace Firm\Application\Service\Firm\Program;

use Resources\Application\Service\Mailer;

class SendClientRegistrationAcceptedMail
{
    /**
     *
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;
    /**
     *
     * @var Mailer
     */
    protected $mailer;
    
    public function __construct(ClientParticipantRepository $clientParticipantRepository, Mailer $mailer)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->mailer = $mailer;
    }
    
    public function execute(string $firmId, string $programId, string $clientId): void
    {
        $this->clientParticipantRepository->ofId($firmId, $programId, $clientId)
                ->sendRegistrationAcceptedMail($this->mailer);
    }

}
