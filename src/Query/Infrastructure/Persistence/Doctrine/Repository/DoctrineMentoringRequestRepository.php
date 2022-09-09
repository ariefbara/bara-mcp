<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestSearch;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMentoringRequestRepository extends DoctrineEntityRepository implements MentoringRequestRepository
{

    public function aMentoringRequestBelongsToParticipant(string $participantId, string $id): MentoringRequest
    {
        $paramenters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->andWhere($qb->expr()->eq('mentoringRequest.id', ':id'))
                ->leftJoin('mentoringRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($paramenters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring request not found');
        }
    }

    public function aMentoringRequestBelongsToPersonnel(string $personnelId, string $id): MentoringRequest
    {
        $paramenters = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->andWhere($qb->expr()->eq('mentoringRequest.id', ':id'))
                ->leftJoin('mentoringRequest.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($paramenters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring request not found');
        }
    }

    public function allMentoringRequestBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringRequestSearch $mentoringRequestSearch)
    {
        $paramenters = [
            'personnelId' => $personnelId,
        ];
        
        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->leftJoin('mentoringRequest.mentor', 'mentor')
                ->andWhere($qb->expr()->eq('mentor.active', 'true'))
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->addOrderBy('mentoringRequest.schedule.startTime', $mentoringRequestSearch->getOrderDirection())
                ->setParameters($paramenters);

        if ($mentoringRequestSearch->getFrom()) {
            $qb->andWhere($qb->expr()->gte('mentoringRequest.schedule.startTime', ':from'))
                    ->setParameter('from', $mentoringRequestSearch->getFrom());
        }
        if ($mentoringRequestSearch->getTo()) {
            $qb->andWhere($qb->expr()->lte('mentoringRequest.schedule.endTime', ':to'))
                    ->setParameter('to', $mentoringRequestSearch->getTo());
        }
        if (!empty($mentoringRequestSearch->getRequestStatusList())) {
            $qb->andWhere($qb->expr()->in('mentoringRequest.requestStatus.value', ':requestStatusList'))
                    ->setParameter('requestStatusList', $mentoringRequestSearch->getRequestStatusList());
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
