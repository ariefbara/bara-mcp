<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\ConsultationRequestRepository,
    Domain\Model\Firm\Program\Participant\ConsultationRequest
};

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository
{
    
    public function ofId(string $consultationRequestId): ConsultationRequest
    {
        return $this->findOneBy(["id" => $consultationRequestId]);
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
