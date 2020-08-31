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
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{
/*
    public function aConsultationSessionOfClient(string $clientId, string $programParticipationId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            'consultationSessionId' => $consultationSessionId,
            'participantId' => $programParticipationId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('consultationSession.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationSessionOfPersonnel(string $firmId, string $personnelId, string $consultantId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            'consultationSessionId' => $consultationSessionId,
            'consultantId' => $consultantId,
            'personnelId' => $personnelId,
            'firmId' => $firmId,
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
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

    public function all(string $firmId, string $programId, string $consultationSetupId,
            ?ConsultationSessionFilter $consultationSessionFilter): ConsultationSession
    {
        $params = [
            'consultationSetupId' => $consultationSetupId,
            'programId' => $programId,
            'firmId' => $firmId,
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

    public function allConsultationSessionsOfClient(string $clientId, string $programParticipationId, int $page,
            int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $params = [
            'participantId' => $programParticipationId,
            'clientId' => $clientId,
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('consultationSession.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params);
        $this->applyFilter($qb, $consultationSessionFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allConsultationSessionsOfPersonnel(string $firmId, string $personnelId, string $consultantId,
            int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $params = [
            'consultantId' => $consultantId,
            'personnelId' => $personnelId,
            'firmId' => $firmId,
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        $this->applyFilter($qb, $consultationSessionFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $consultationSetupId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            'consultationSessionId' => $consultationSessionId,
            'consultationSetupId' => $consultationSetupId,
            'programId' => $programId,
            'firmId' => $firmId,
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

 * 
 */
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
    
    public function aConsultationSessionOfClient(string $firmId, string $clientId, string $programId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
            'consultationSessionId' => $consultationSessionId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('clientParticipant.program', 'program')
                ->andWhere($clientParticipantQb->expr()->eq('program.id', ':programId'))
                ->leftJoin('client.firm', 'cFirm')
                ->leftJoin('program.firm', 'pFirm')
                ->andWhere($clientParticipantQb->expr()->eq('cFirm.id', ':firmId'))
                ->andWhere($clientParticipantQb->expr()->eq('pFirm.id', ':firmId'))
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

    public function aConsultationSessionOfPersonnel(string $firmId, string $personnelId, string $programId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programId' => $programId,
            'consultationSessionId' => $consultationSessionId,
        ];
        
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.consultant', 'consultant')
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('consultant.personnel', 'personnel')
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

    public function all(string $firmId, string $programId, string $consultationSetupId,
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

    public function allConsultationSessionsOfPersonnel(string $firmId, string $personnelId, string $programId,
            int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programId' => $programId,
        ];
        
        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.consultant', 'consultant')
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        $this->applyFilter($qb, $consultationSessionFilter);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allConsultationsSessionOfClient(string $firmId, string $clientId, string $programId, int $page,
            int $pageSize,
            ConsultationSessionFilter $consultationSessionFilter)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programId' => $programId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('tParticipant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'tParticipant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('clientParticipant.program', 'program')
                ->andWhere($clientParticipantQb->expr()->eq('program.id', ':programId'))
                ->leftJoin('client.firm', 'cFirm')
                ->leftJoin('program.firm', 'pFirm')
                ->andWhere($clientParticipantQb->expr()->eq('cFirm.id', ':firmId'))
                ->andWhere($clientParticipantQb->expr()->eq('pFirm.id', ':firmId'))
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

}
