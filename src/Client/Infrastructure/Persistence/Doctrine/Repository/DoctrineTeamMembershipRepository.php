<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\TeamMembershipRepository,
    Domain\Model\Client\TeamMembership
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineTeamMembershipRepository extends EntityRepository implements TeamMembershipRepository
{
    
    public function ofId(string $firmId, string $clientId, string $teamMembershipId): TeamMembership
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
        ];
        
        $qb = $this->createQueryBuilder("teamMembership");
        $qb->select("teamMembership")
                ->andWhere($qb->expr()->eq("teamMembership.id", ":teamMembershipId"))
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team membership not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
