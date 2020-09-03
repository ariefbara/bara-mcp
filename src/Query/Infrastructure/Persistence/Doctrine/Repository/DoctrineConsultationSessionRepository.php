<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException,
    QueryBuilder
};
use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionFilter,
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Domain\Model\User\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{

    protected function applyFilter(QueryBuilder $qb, ?ConsultationSessionFilter $consultationSessionFilter): void
    {
        if (empty($consultationSessionFilter)) {
            return;
        }

        if (!empty($minStartTime = $consultationSessionFilter->getMinStartTime())) {
            $qb->andWhere($qb->expr()->gte('consultationSession.startEndTime.startDateTime', ':minStartTime'))
                    ->setParameter('minStartTime', $minStartTime);
        }
        if (!empty($maxStartTime = $consultationSessionFilter->getMaxStartTime())) {
            $qb->andWhere($qb->expr()->lt('consultationSession.startEndTime.startDateTime', ':maxStartTime'))
                    ->setParameter('maxStartTime', $maxStartTime);
        }
        if (!empty($containConsultantFeedback = $consultationSessionFilter->isContainConsultantFeedback())) {
            if ($containConsultantFeedback) {
                $qb->andWhere($qb->expr()->isNotNull('consultationSession.consultantFeedback'));
            } else {
                $qb->andWhere($qb->expr()->isNull('consultationSession.consultantFeedback'));
            }
        }
        if (!empty($containParticipantFeedback = $consultationSessionFilter->isContainParticipantFeedback())) {
            if ($containParticipantFeedback) {
                $qb->andWhere($qb->expr()->isNotNull('consultationSession.participantFeedback'));
            } else {
                $qb->andWhere($qb->expr()->isNull('consultationSession.participantFeedback'));
            }
        }
    }

    public function aConsultationSessionOfClient(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'consultationSessionId' => $consultationSessionId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationSessionOfPersonnel(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
            'consultationSessionId' => $consultationSessionId,
        ];
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.consultant', 'programConsultation')
                ->andWhere($qb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(
            string $firmId, string $programId, string $consultationSetupId,
            ?ConsultationSessionFilter $consultationSessionFilter): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'consultationSetupId' => $consultationSetupId,
        ];
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.consultationSetup', 'consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->leftJoin('consultationSetup.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        $this->applyFilter($qb, $consultationSessionFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allConsultationSessionsOfPersonnel(
            string $firmId, string $personnelId, string $programConsultationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
        ];
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.consultant', 'programConsultation')
                ->andWhere($qb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        $this->applyFilter($qb, $consultationSessionFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allConsultationsSessionOfClient(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ConsultationSessionFilter $consultationSessionFilter)
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
                ->leftJoin('clientParticipant.client', 'client')
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params);
        
        $this->applyFilter($qb, $consultationSessionFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $consultationSetupId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'consultationSetupId' => $consultationSetupId,
            'consultationSessionId' => $consultationSessionId,
        ];
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.consultationSetup', 'consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->leftJoin('consultationSetup.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationSessionFromUserParticipant(string $userId, string $userParticipantId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'consultationSessionId' => $consultationSessionId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationSessionFromUserParticipant(string $userId, string $userParticipantId, int $page,
            int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
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

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params);
        
        $this->applyFilter($qb, $consultationSessionFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
