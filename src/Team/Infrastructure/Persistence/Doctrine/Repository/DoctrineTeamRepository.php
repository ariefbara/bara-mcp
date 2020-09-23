<?php

namespace Team\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Uuid;
use Team\ {
    Application\Service\TeamRepository,
    Domain\Model\Team
};

class DoctrineTeamRepository extends EntityRepository implements TeamRepository
{

    public function add(Team $team): void
    {
        $em = $this->getEntityManager();
        $em->persist($team);
        $em->flush();
    }

    public function isNameAvailable(string $firmId, string $name): bool
    {
        $params = [
            "firmId" => $firmId,
            "name" => $name,
        ];

        $qb = $this->createQueryBuilder("client");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("client.name", ":name"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        return empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
