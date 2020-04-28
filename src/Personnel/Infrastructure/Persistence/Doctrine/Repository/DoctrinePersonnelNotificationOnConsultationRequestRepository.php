<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequest\PersonnelNotificationOnConsultationRequestRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest\PersonnelNotificationOnConsultationRequest
};
use Resources\Uuid;

class DoctrinePersonnelNotificationOnConsultationRequestRepository extends EntityRepository implements PersonnelNotificationOnConsultationRequestRepository
{

    public function add(PersonnelNotificationOnConsultationRequest $personnelNotificationOnConsultationRequest): void
    {
        $em = $this->getEntityManager();
        $em->persist($personnelNotificationOnConsultationRequest);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
