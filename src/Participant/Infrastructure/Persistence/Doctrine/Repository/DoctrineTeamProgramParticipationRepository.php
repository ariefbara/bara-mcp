<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\Client\AsTeamMember\TeamParticipantRepository;
use Participant\Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository;
use Participant\Domain\Model\TeamProgramParticipation;
use Resources\Exception\RegularException;

class DoctrineTeamProgramParticipationRepository extends EntityRepository implements TeamProgramParticipationRepository,
        TeamParticipantRepository
{

    public function ofId(string $teamProgramParticipationId): TeamProgramParticipation
    {
        $params = [
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];

        $qb = $this->createQueryBuilder("teamProgramParticipation");
        $qb->select("teamProgramParticipation")
                ->andWhere($qb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
