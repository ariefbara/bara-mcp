<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\TeamParticipantRepository;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineTeamParticipantRepository extends DoctrineEntityRepository implements TeamParticipantRepository
{
    
    public function aTeamParticipantCorrespondWitnProgram(string $teamId, string $programId): TeamParticipant
    {
        $params = [
            "teamId" => $teamId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("teamParticipant");
        $qb->select("teamParticipant")
                ->andWhere($qb->expr()->eq("teamParticipant.teamId", ":teamId"))
                ->leftJoin("teamParticipant.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $id): TeamParticipant
    {
        return $this->findOneByIdOrDie($id, 'team participant');
    }

}
