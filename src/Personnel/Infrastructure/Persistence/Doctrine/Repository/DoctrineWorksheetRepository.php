<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Program\Participant\WorksheetRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use Resources\Exception\RegularException;

class DoctrineWorksheetRepository extends EntityRepository implements WorksheetRepository
{

    public function aWorksheetInProgramsWhereConsultantInvolved(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId,
            string $worksheetId): Worksheet
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
            'participantId' => $participantId,
            'worksheetId' => $worksheetId,
        ];
        
        $programConsultationQb = $this->getEntityManager()->createQueryBuilder();
        $programConsultationQb->select('programConsultation.programId')
                ->from(ProgramConsultant::class, 'programConsultation')
                ->andWhere($programConsultationQb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($programConsultationQb->expr()->eq('personnel.id', ':personnelId'))
                ->andWhere($programConsultationQb->expr()->eq('personnel.firmId', ':firmId'))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder('worksheet');
        $qb->select('worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->andWhere($qb->expr()->in('participant.programId', $programConsultationQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: worksheet not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
