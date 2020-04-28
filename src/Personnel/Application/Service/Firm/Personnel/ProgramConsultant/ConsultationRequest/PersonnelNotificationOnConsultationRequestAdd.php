<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository,
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel\PersonnelNotification,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest\PersonnelNotificationOnConsultationRequest
};
use Shared\Domain\Model\Notification;

class PersonnelNotificationOnConsultationRequestAdd
{

    /**
     *
     * @var PersonnelNotificationOnConsultationRequestRepository
     */
    protected $personnelNotificationOnConsultationRequestRepository;

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    function __construct(
            PersonnelNotificationOnConsultationRequestRepository $personnelNotificationOnConsultationRequestRepository,
            ConsultationRequestRepository $consultationRequestRepository, PersonnelRepository $personnelRepository)
    {
        $this->personnelNotificationOnConsultationRequestRepository = $personnelNotificationOnConsultationRequestRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->personnelRepository = $personnelRepository;
    }

    public function execute(string $consultationRequestId, string $message): void
    {
        $consultationRequest = $this->consultationRequestRepository->aConsultationRequestById($consultationRequestId);
        $id = $this->personnelNotificationOnConsultationRequestRepository->nextIdentity();
        
        $personnel = $this->personnelRepository->aPersonnelHavingConsultationRequest($consultationRequestId);
        $notification = new Notification($id, $message);
        $personnelNotification = new PersonnelNotification($personnel, $id, $notification);

        $personnelNotificationOnConsultationRequest = new PersonnelNotificationOnConsultationRequest(
                $consultationRequest, $id, $personnelNotification);
        $this->personnelNotificationOnConsultationRequestRepository->add($personnelNotificationOnConsultationRequest);
    }

}
