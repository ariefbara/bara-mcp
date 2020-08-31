<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\ClientParticipant\Worksheet\ConsultantCommentRepository,
    Domain\Model\Firm\Program\ClientParticipant,
    Domain\Model\Firm\Program\Participant\Worksheet\ConsultantComment
};
use Resources\Exception\RegularException;

class DoctrineConsultantCommentRepository extends EntityRepository implements ConsultantCommentRepository
{
/*
    public function aCommentFromProgramConsultant(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultantCommentId): ConsultantComment
    {
        $params = [
            'consultantCommentId' => $consultantCommentId,
            'consultantId' => $programConsultantCompositionId->getProgramConsultantId(),
            'personnelId' => $programConsultantCompositionId->getPersonnelId(),
            'firmId' => $programConsultantCompositionId->getFirmId(),
        ];
        
        $qb = $this->createQueryBuilder('consultantComment');
        $qb->select('consultantComment')
                ->andWhere($qb->expr()->eq('consultantComment.id', ':consultantCommentId'))
                ->leftJoin('consultantComment.consultant', 'consultant')
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
            $errorDetail = 'not found: consultant comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }
 * 
 */
    public function aConsultantCommentOfClientParticipant(string $firmId, string $programId, string $clientId,
            string $worksheetId, string $consultantCommentId): ConsultantComment
    {
        $params = [
            'firmId' =>  $firmId,
            'programId' =>  $programId,
            'clientId' =>  $clientId,
            'worksheetId' =>  $worksheetId,
            'consultantCommentId' => $consultantCommentId,
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
        
        $qb = $this->createQueryBuilder('consultantComment');
        $qb->select('consultantComment')
                ->andWhere($qb->expr()->eq('consultantComment.id', ':consultantCommentId'))
                ->leftJoin('consultantComment.worksheet', 'worksheet')
                ->andWhere($qb->expr()->eq('worksheet.id', ':worksheetId'))
                ->leftJoin('worksheet.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found; consultant comment not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
