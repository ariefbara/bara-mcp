<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotificationRepository,
    Domain\Model\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotification
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineConsultationSessionNotificationRepository extends EntityRepository implements ConsultationSessionNotificationRepository
{

    public function add(ConsultationSessionNotification $consultationSessionNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationSessionNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
