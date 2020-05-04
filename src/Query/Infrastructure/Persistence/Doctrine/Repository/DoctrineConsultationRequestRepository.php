<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\{
    Application\Service\Client\ProgramParticipation\ConsultationRequestRepository as InterfaceForClient,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository as InterfaceForPersonnel,
    Application\Service\Firm\Program\Participant\ConsultationRequestRepository,
    Application\Service\Firm\Program\Participant\ParticipantCompositionId,
    Domain\Model\Firm\Program\Participant\ConsultationRequest
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository, InterfaceForClient,
        InterfaceForPersonnel
{

    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $participantCompositionId->getParticipantId(),
            "programId" => $participantCompositionId->getProgramId(),
            "firmId" => $participantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.participant', 'participant')
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

    public function ofId(ParticipantCompositionId $participantCompositionId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            "consultationRequestId" => $consultationRequestId,
            "participantId" => $participantCompositionId->getParticipantId(),
            "programId" => $participantCompositionId->getProgramId(),
            "firmId" => $participantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.participant', 'participant')
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
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aConsultationRequestOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            "consultationRequestId" => $consultationRequestId,
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestsOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aConsultationRequestOfPersonnel(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            "consultationRequestId" => $consultationRequestId,
            "consultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.consultant', 'consultant')
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
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestsOfPersonnel(
            ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize)
    {
        $params = [
            "consultantId" => $programConsultantCompositionId->getProgramConsultantId(),
            "personnelId" => $programConsultantCompositionId->getPersonnelId(),
            "firmId" => $programConsultantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.consultant', 'consultant')
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
