<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\ {
    Application\Service\User\ProgramParticipant\UserParticipantRepository,
    Domain\Model\Firm\Program\UserParticipant
};

class DoctrineUserParticipantRepository extends EntityRepository implements UserParticipantRepository
{
    
    public function aUserParticipantCorrespondWithProgram(string $userId, string $programId): UserParticipant
    {
        $params = [
            "userId" => $userId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("userParticipant");
        $qb->select("userParticipant")
                ->leftJoin("userParticipant.user", "user")
                ->andWhere($qb->expr()->eq("user.id", ":userId"))
                ->leftJoin("userParticipant.participant", "participant")
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

}
