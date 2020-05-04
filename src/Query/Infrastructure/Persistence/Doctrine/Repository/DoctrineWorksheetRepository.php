<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\Application\Service\Firm\Program\Participant\WorksheetRepository as InterfaceForPersonnelBC;
use Query\ {
    Application\Service\Client\ProgramParticipation\WorksheetRepository as InterfaceForProgramParticipation,
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Program\Participant\ParticipantCompositionId,
    Application\Service\Firm\Program\Participant\WorksheetRepository,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineWorksheetRepository extends EntityRepository implements WorksheetRepository, InterfaceForProgramParticipation,
        InterfaceForPersonnelBC
{

    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $participantCompositionId->getParticipantId(),
            "programId" => $participantCompositionId->getProgramId(),
            "firmId" => $participantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->leftJoin('worksheet.participant', 'participant')
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

    public function ofId(ParticipantCompositionId $participantCompositionId, string $worksheetId): Worksheet
    {
        $params = [
            "worksheetId" => $worksheetId,
            "participantId" => $participantCompositionId->getParticipantId(),
            "programId" => $participantCompositionId->getProgramId(),
            "firmId" => $participantCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
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
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aWorksheetOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet
    {
        $params = [
            "worksheetId" => $worksheetId,
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allWorksheetsOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $programParticipationCompositionId->getProgramParticipationId(),
            "clientId" => $programParticipationCompositionId->getClientId(),
        ];

        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aWorksheetInProgramsWhereConsultantInvolved(
            PersonnelCompositionId $personnelCompositionId, string $consultantId, string $participantId,
            string $worksheetId): Worksheet
    {
        $parameters = [
            "worksheetId" => $worksheetId,
            "participantId" => $participantId,
            "consultantId" => $consultantId,
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];
        
        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tProgram.id')
                ->from(Consultant::class, "consultant")
                ->andWhere($subQuery->expr()->eq('consultant.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($subQuery->expr()->eq('personnel.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($subQuery->expr()->eq('firm.id', ':firmId'))
                ->leftJoin('consultant.program', 'tProgram')
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.removed', 'false'))
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: worksheet not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
