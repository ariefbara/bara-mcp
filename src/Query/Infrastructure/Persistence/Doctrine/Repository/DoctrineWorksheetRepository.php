<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Program\Participant\WorksheetRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Model\User\UserParticipant
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineWorksheetRepository extends EntityRepository implements WorksheetRepository
{

    public function all(string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'participantId' => $participantId,
        ];

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $participantId, string $worksheetId): Worksheet
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
        ];

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: worksheet not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aWorksheetBelongsToClient(
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId): Worksheet
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'worksheetId' => $worksheetId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: worksheet not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allWorksheetsBelongsToClient(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?string $missionId, ?string $parentWorksheetId)
    {

        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params);

        if (!empty($missionId)) {
            $qb->leftJoin('worksheet.mission', 'mission')
                    ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                    ->setParameter('missionId', $missionId);
        }

        if (!empty($parentWorksheetId)) {
            $qb->leftJoin('worksheet.parent', 'parent')
                    ->andWhere($qb->expr()->eq('parent.id', ':parentId'))
                    ->setParameter('parentId', $parentWorksheetId);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aWorksheetBelongsToUserParticipant(string $userId, string $userParticipantId, string $worksheetId): Worksheet
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'worksheetId' => $worksheetId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: worksheet not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allWorksheetBelongsToUserParticipant(string $userId, string $userParticipantId, int $page,
            int $pageSize, ?string $missionId, ?string $parentWorksheetId)
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params);

        if (!empty($missionId)) {
            $qb->leftJoin('worksheet.mission', 'mission')
                    ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                    ->setParameter('missionId', $missionId);
        }

        if (!empty($parentWorksheetId)) {
            $qb->leftJoin('worksheet.parent', 'parent')
                    ->andWhere($qb->expr()->eq('parent.id', ':parentId'))
                    ->setParameter('parentId', $parentWorksheetId);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }


}
