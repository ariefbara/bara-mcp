<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\Client\AsTeamMember\TeamRegistrantRepository;
use Participant\Application\Service\TeamProgramRegistrationRepository;
use Participant\Domain\Model\TeamProgramRegistration;
use Resources\Exception\RegularException;
use Resources\Uuid;

class DoctrineTeamProgramRegistrationRepository extends EntityRepository implements TeamProgramRegistrationRepository, TeamRegistrantRepository
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
