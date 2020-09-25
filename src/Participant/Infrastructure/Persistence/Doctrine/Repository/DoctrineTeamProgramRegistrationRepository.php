<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\TeamProgramRegistrationRepository,
    Domain\Model\TeamProgramRegistration
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineTeamProgramRegistrationRepository extends EntityRepository implements TeamProgramRegistrationRepository
{
    
    public function add(TeamProgramRegistration $teamProgramRegistration): void
    {
        $em = $this->getEntityManager();
        $em->persist($teamProgramRegistration);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $params = [
            "teamProgramRegistrationId" => $teamProgramRegistrationId,
        ];
        
        $qb = $this->createQueryBuilder("teamProgramRegistration");
        $qb->select("teamProgramRegistration")
                ->andWhere($qb->expr()->eq("teamProgramRegistration.id", ":teamProgramRegistrationId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team program registration  not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
