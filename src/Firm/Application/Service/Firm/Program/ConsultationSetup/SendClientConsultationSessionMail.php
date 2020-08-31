<?php

namespace Firm\Application\Service\Firm\Program\ConsultationSetup;

use Resources\Application\Service\Mailer;

class SendClientConsultationSessionMail
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    /**
     *
     * @var Mailer
     */
    protected $mailer;

    public function __construct(ConsultationSessionRepository $consultationSessionRepository, Mailer $mailer)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->mailer = $mailer;
    }

    public function execute(string $firmId, string $clientId, string $programId, string $consultationSessionId): void
    {
        $this->consultationSessionRepository
                ->aConsultationSessionOfClient($firmId, $clientId, $programId, $consultationSessionId)
                ->sendMail($this->mailer);
    }

}
