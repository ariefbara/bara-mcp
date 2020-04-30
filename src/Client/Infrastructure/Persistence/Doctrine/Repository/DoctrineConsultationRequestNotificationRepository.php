<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotificationRepository,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotification
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineConsultationRequestNotificationRepository extends EntityRepository implements ConsultationRequestNotificationRepository
{
    
    public function add(ConsultationRequestNotification $consultationRequestNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationRequestNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
