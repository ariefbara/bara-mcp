<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Coordinator\OKRPeriodRepository;
use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineOKRPeriodRepository extends DoctrineEntityRepository implements OKRPeriodRepository
{
    
    public function ofId(string $okrPeriodId): OKRPeriod
    {
        return $this->findOneByIdOrDie($okrPeriodId, 'okr period');
    }

}
