<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\User\ProgramParticipationRepository,
    Domain\Model\User\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineUserParticipantRepository extends EntityRepository implements ProgramParticipationRepository
{

    public function all(string $userId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            'userId' => $userId,
        ];

        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->leftJoin("userParticipant.participant", "participant")
                    ->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $userId, string $userParticipantId): UserParticipant
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
        ];

        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->andWhere($qb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program participation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aProgramParticipationOfUserCorrespondWithProgram(string $userId, string $programId): UserParticipant
    {
        $params = [
            "userId" => $userId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("userProgramParticipation");
        $qb->select("userProgramParticipation")
                ->leftJoin("userProgramParticipation.user", "user")
                ->andWhere($qb->expr()->eq("user.id", ":userId"))
                ->leftJoin("userProgramParticipation.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user program participation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
