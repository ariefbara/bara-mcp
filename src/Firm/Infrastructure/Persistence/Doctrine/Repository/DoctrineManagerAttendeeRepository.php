<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Manager\ManagerAttendee\ManagerAttendeeRepository;
use Firm\Domain\Model\Firm\Manager\ManagerAttendee;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineManagerAttendeeRepository extends DoctrineEntityRepository implements ManagerAttendeeRepository
{
    
    public function aManagerAttendeeBelongsToManager(string $firmId, string $managerId, string $meetingId): ManagerAttendee
    {
        $params = [
            'firmId' => $firmId,
            'managerId' => $managerId,
            'meetingId' => $meetingId,
        ];
        
        $qb = $this->createQueryBuilder('managerAttendee');
        $qb->select('managerAttendee')
                ->leftJoin('managerAttendee.attendee', 'attendee')
                ->leftJoin('attendee.meeting', 'meeting')
                ->andWhere($qb->expr()->eq('meeting.id', ':meetingId'))
                ->leftJoin('managerAttendee.manager', 'manager')
                ->andWhere($qb->expr()->eq('manager.id', ':managerId'))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: manager attendee not found');
        }
    }

}
