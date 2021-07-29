<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\User\ProgramParticipant\UserParticipantRepository;
use Firm\Domain\Model\Firm\Program\UserParticipant;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineUserParticipantRepository extends DoctrineEntityRepository implements UserParticipantRepository
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

    public function aUserParticipantBelongsToUser(string $userId, string $participantId): UserParticipant
    {
        $params = [
            'userId' => $userId,
            'participantId' => $participantId,
        ];
        
        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->andWhere($qb->expr()->eq('userParticipant.id', ':participantId'))
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: user participant not found');
        }
    }

}
