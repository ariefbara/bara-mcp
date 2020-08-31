<?php

namespace Firm\Application\Service\Firm\Program;

use Resources\Application\Service\Mailer;

class SendUserRegistrationAcceptedMail
{

    /**
     *
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var Mailer
     */
    protected $mailer;

    public function __construct(UserParticipantRepository $userParticipantRepository, Mailer $mailer)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->mailer = $mailer;
    }

    public function execute(string $firmId, string $programId, string $userId): void
    {
        $this->userParticipantRepository->ofId($firmId, $programId, $userId)
                ->sendRegistrationAcceptedMail($this->mailer);
    }

}
