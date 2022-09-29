<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\User\AsProgramParticipant\UserParticipantRepository;
use Query\Application\Service\User\ProgramParticipationRepository;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\User\UserParticipantRepository as UserParticipantRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineUserParticipantRepository extends EntityRepository implements ProgramParticipationRepository, UserParticipantRepository,
        UserParticipantRepository2
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

    public function aUserParticipant(string $userId, string $participantId): UserParticipant
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
            $errorDetail = 'not found: user participant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aUserParticipantInProgram(string $programId, string $id): UserParticipant
    {
        $parameters = [
            'programId' => $programId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->andWhere($qb->expr()->eq('userParticipant.id', ':id'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('user participant not found');
        }
    }

}
