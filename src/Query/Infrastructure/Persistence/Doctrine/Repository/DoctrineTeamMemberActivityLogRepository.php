<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Client\AsTeamMember\TeamMemberActivityLogRepository;
use Query\Domain\Model\Firm\Team\Member\TeamMemberActivityLog;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineTeamMemberActivityLogRepository extends EntityRepository implements TeamMemberActivityLogRepository
{
    
    public function allActivityLogsOfTeamMember(string $memberId, int $page, int $pageSize)
    {
        $params = [
            'memberId' => $memberId,
        ];
        
        $qb = $this->createQueryBuilder('teamMemberActivityLog');
        $qb->select('teamMemberActivityLog')
                ->leftJoin('teamMemberActivityLog.member', 'T_member')
                ->andWhere($qb->expr()->eq('T_member.id', ':memberId'))
                ->setParameters($params);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function anActivityLogOfTeamMember(string $memberId, string $teamMemberActivityLogId): TeamMemberActivityLog
    {
        $params = [
            'memberId' => $memberId,
            'id' => $teamMemberActivityLogId,
        ];
        
        $qb = $this->createQueryBuilder('teamMemberActivityLog');
        $qb->select('teamMemberActivityLog')
                ->andWhere($qb->expr()->eq('teamMemberActivityLog.id', ':id'))
                ->leftJoin('teamMemberActivityLog.member', 'T_member')
                ->andWhere($qb->expr()->eq('T_member.id', ':memberId'))
                ->setParameters($params)
                ->setMaxResults(1);
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: team member activity log not found');
        }
    }

}
