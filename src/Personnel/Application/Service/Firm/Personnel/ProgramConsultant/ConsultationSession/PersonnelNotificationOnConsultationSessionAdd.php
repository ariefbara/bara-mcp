<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\{
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository,
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel\PersonnelNotification,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\PersonnelNotificationOnConsultationSession
};
use Shared\Domain\Model\Notification;

class PersonnelNotificationOnConsultationSessionAdd
{

    /**
     *
     * @var PersonnelNotificationOnConsultationSessionRepository
     */
    protected $personnelNotificationOnConsultationSessionRepository;

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    function __construct(PersonnelNotificationOnConsultationSessionRepository $personnelNotificationOnConsultationSessionRepository,
            ConsultationSessionRepository $consultationSessionRepository, PersonnelRepository $personnelRepository)
    {
        $this->personnelNotificationOnConsultationSessionRepository = $personnelNotificationOnConsultationSessionRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->personnelRepository = $personnelRepository;
    }

    public function execute(string $consultationSessionId, string $message): void
    {
        $consultationSession = $this->consultationSessionRepository->aConsultationSessionById($consultationSessionId);
        $id = $this->personnelNotificationOnConsultationSessionRepository->nextIdentity();

        $personnel = $this->personnelRepository->aPersonnelHavingConsultationSession($consultationSessionId);
        $notification = new Notification($id, $message);
        $personnelNotification = new PersonnelNotification($personnel, $id, $notification);

        $personnelNotificationOnConsultationSession = new PersonnelNotificationOnConsultationSession(
                $consultationSession, $id, $personnelNotification);
        $this->personnelNotificationOnConsultationSessionRepository->add($personnelNotificationOnConsultationSession);
    }

}
