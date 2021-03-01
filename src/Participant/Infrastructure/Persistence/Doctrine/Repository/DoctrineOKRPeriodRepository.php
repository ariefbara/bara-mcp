<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriod;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineOKRPeriodRepository extends DoctrineEntityRepository implements OKRPeriodRepository
{
    
    public function add(OKRPeriod $okrPeriod): void
    {
        $this->persist($okrPeriod);
    }

    public function ofId(string $okrPeriodId): OKRPeriod
    {
        return $this->findOneByIdOrDie($okrPeriodId, 'okr period');
    }

}
