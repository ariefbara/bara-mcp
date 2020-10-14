<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Participant\ {
    Application\Service\Participant\ViewLearningMaterialActivityLogRepository,
    Domain\Model\Participant\ViewLearningMaterialActivityLog
};
use Resources\Uuid;

class DoctrineViewLearningMaterialActivityLogRepository extends EntityRepository implements ViewLearningMaterialActivityLogRepository
{
    
    public function add(ViewLearningMaterialActivityLog $viewLearningMaterialActivityLog): void
    {
        $em = $this->getEntityManager();
        $em->persist($viewLearningMaterialActivityLog);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
