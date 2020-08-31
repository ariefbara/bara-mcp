<?php

namespace Firm\Application\Service\Firm\Program\ConsultationSetup;

use Resources\Application\Service\Mailer;

class SendClientConsultationRequestMail
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var Mailer
     */
    protected $mailer;

    public function __construct(ConsultationRequestRepository $consultationRequestRepository, Mailer $mailer)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->mailer = $mailer;
    }

    public function execute(string $firmId, string $clientId, string $programId, string $consultationRequestId): void
    {
        $this->consultationRequestRepository
                ->aConsultationRequestOfClient($firmId, $clientId, $programId, $consultationRequestId)
                ->sendMail($this->mailer);
    }

}
