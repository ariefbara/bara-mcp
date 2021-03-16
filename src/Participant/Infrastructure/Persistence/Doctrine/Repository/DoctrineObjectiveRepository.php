<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineObjectiveRepository extends DoctrineEntityRepository implements ObjectiveRepository
{

    public function ofId(string $objectiveId): Objective
    {
        return $this->findOneByIdOrDie($objectiveId, 'objective');
    }

}
