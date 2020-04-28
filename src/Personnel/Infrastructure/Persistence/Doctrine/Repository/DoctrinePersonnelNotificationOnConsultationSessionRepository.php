<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession\PersonnelNotificationOnConsultationSessionRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession\PersonnelNotificationOnConsultationSession
};
use Resources\Uuid;

class DoctrinePersonnelNotificationOnConsultationSessionRepository extends EntityRepository implements PersonnelNotificationOnConsultationSessionRepository
{

    public function add(PersonnelNotificationOnConsultationSession $personnelNotificationOnConsultationSession): void
    {
        $em = $this->getEntityManager();
        $em->persist($personnelNotificationOnConsultationSession);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
