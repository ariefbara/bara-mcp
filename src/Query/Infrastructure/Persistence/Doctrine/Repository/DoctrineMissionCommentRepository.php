<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Task\Dependency\Firm\Program\Mission\MissionCommentFilter;
use Query\Domain\Task\Dependency\Firm\Program\Mission\MissionCommentRepository as MissionCommentRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMissionCommentRepository extends DoctrineEntityRepository implements MissionCommentRepository, MissionCommentRepository2
{
    
    public function aMissionCommentInProgram(string $programId, string $missionCommentId): MissionComment
    {
        $params = [
            'programId' => $programId,
            'missionCommentId' => $missionCommentId,
        ];
        
        $qb = $this->createQueryBuilder('missionComment');
        $qb->select('missionComment')
                ->andWhere($qb->expr()->eq('missionComment.id', ':missionCommentId'))
                ->leftJoin('missionComment.mission', 'mission')
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mission comment not found');
        }
    }

    public function allMissionCommentsBelongsInMission(string $programId, string $missionId, int $page, int $pageSize)
    {
        $params = [
            'programId' => $programId,
            'missionId' => $missionId,
        ];
        
        $qb = $this->createQueryBuilder('missionComment');
        $qb->select('missionComment')
                ->leftJoin('missionComment.mission', 'mission')
                ->andWhere($qb->expr()->eq('mission.id', ':missionId'))
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->addOrderBy('missionComment.modifiedTime', 'ASC')
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allMissionCommentInProgram(string $programId, MissionCommentFilter $filter)
    {
        $params = [
            'programId' => $programId,
        ];
        
        $qb = $this->createQueryBuilder('missionComment');
        $qb->select('missionComment')
                ->leftJoin('missionComment.mission', 'mission')
                ->leftJoin('mission.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->addOrderBy('missionComment.modifiedTime', $filter->getOrder())
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $filter->getPagination()->getPage(), $filter->getPagination()->getPageSize());
    }

}
