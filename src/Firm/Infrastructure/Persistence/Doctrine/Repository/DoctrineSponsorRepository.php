<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Domain\Model\Firm\Program\Sponsor;
use Firm\Domain\Task\Dependency\Firm\SponsorRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineSponsorRepository extends DoctrineEntityRepository implements SponsorRepository
{
    
    public function add(Sponsor $sponsor): void
    {
        $this->getEntityManager()->persist($sponsor);
    }

    public function ofId(string $id): Sponsor
    {
        return $this->findOneByIdOrDie($id, 'sponsor');
    }

}
