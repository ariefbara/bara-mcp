<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Client\ProgramParticipation\ConsultationSessionRepository as InterfaceForParticipant,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository as InterfaceForPersonnel,
    Application\Service\Firm\Program\Participant\ConsultationSessionRepository,
    Application\Service\Firm\Program\Participant\ParticipantCompositionId,
    Domain\Model\Firm\Program\Participant\ConsultationSession
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository, InterfaceForParticipant,
        InterfaceForPersonnel
{

    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $participantCompositionId->getParticipantId(),
            "programId" => $participantCompositionId->getProgramId(),
            "firmId" => $participantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(ParticipantCompositionId $participantCompositionId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            "consultationSessionId" => $consultationSessionId,
            "participantId" => $participantCompositionId->getParticipantId(),
            "programId" => $participantCompositionId->getProgramId(),
            "firmId" => $participantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation session not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationSessionOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationSessionId): ConsultationSession
    {
        $params = [
            "consultationSessionId" => $consultationSessionId,
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation session not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationSessionsOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aConsultationSessionOfPersonnel(\Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId $programConsultantCompositionId,
            string $consultationSessionId): ConsultationSession
    {
        $params = [
            "consultationSessionId" => $consultationSessionId,
            'consultantId' => $programConsultantCompositionId->getProgramConsultantId(),
            'personnelId' => $programConsultantCompositionId->getPersonnelId(),
            'firmId' => $programConsultantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation session not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationSessionsOfPersonnel(\Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId $programConsultantCompositionId,
            int $page, int $pageSize)
    {
        $params = [
            'consultantId' => $programConsultantCompositionId->getProgramConsultantId(),
            'personnelId' => $programConsultantCompositionId->getPersonnelId(),
            'firmId' => $programConsultantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
