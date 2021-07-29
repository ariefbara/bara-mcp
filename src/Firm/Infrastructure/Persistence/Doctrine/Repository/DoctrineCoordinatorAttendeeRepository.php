<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Personnel\CoordinatorAttendee\CoordinatorAttendeeRepository;
use Firm\Domain\Model\Firm\Program\Coordinator\CoordinatorAttendee;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineCoordinatorAttendeeRepository extends DoctrineEntityRepository implements CoordinatorAttendeeRepository
{
    
    public function aCoordinatorAttendeeBelongsToPersonnel(string $firmId, string $personnelId, string $meetingId): CoordinatorAttendee
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'meetingId' => $meetingId,
        ];
        
        $qb = $this->createQueryBuilder('coordinatorAttendee');
        $qb->select('coordinatorAttendee')
                ->leftJoin('coordinatorAttendee.attendee', 'attendee')
                ->leftJoin('attendee.meeting', 'meeting')
                ->andWhere($qb->expr()->eq('meeting.id', ':meetingId'))
                ->leftJoin('coordinatorAttendee.coordinator', 'coordinator')
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: coordinator attendee not found');
        }
    }

}
