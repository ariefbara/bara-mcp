<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Listener\TeamRepository as TeamRepository2;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Task\Dependency\Firm\TeamRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineTeamRepository extends DoctrineEntityRepository implements TeamRepository, TeamRepository2
{

    public function add(Team $team): void
    {
        $this->getEntityManager()->persist($team);
    }

    public function ofId(string $id): Team
    {
        return $this->findOneByIdOrDie($id, 'team');
    }

}
