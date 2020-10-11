<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\ConsultationSessionRepository,
    Domain\Model\Firm\Program\Participant\ConsultationSession
};

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{
    
    public function ofId(string $consultationSessionId): ConsultationSession
    {
        return $this->findOneBy(["id" => $consultationSessionId]);
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
