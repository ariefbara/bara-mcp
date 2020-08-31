<?php

namespace Firm\Application\Service\Firm;

use Resources\Application\Service\Mailer;

class SendClientActivationCodeMail
{
    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;
    /**
     *
     * @var Mailer
     */
    protected $mailer;
    
    public function __construct(ClientRepository $clientRepository, Mailer $mailer)
    {
        $this->clientRepository = $clientRepository;
        $this->mailer = $mailer;
    }
    
    public function execute(string $firmId, string $clientId): void
    {
        $this->clientRepository->ofId($firmId, $clientId)->sendActivationCodeMail($this->mailer);
    }

}
