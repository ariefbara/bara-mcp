<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Service\Personnel\ConsultantAttendee\ConsultantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineConsultantAttendeeRepository extends DoctrineEntityRepository implements ConsultantAttendeeRepository
{
    
    public function aConsultantAttendeeBelongsToPersonnel(string $firmId, string $personnelId, string $meetingId): ConsultantAttendee
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'meetingId' => $meetingId,
        ];
        
        $qb = $this->createQueryBuilder('consultantAttendee');
        $qb->select('consultantAttendee')
                ->leftJoin('consultantAttendee.attendee', 'attendee')
                ->leftJoin('attendee.meeting', 'meeting')
                ->andWhere($qb->expr()->eq('meeting.id', ':meetingId'))
                ->leftJoin('consultantAttendee.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: consultant attendee not found');
        }
    }

}
