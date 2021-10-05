<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Sponsor;
use Query\Domain\Task\Dependency\Firm\Program\SponsorRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineSponsorRepository extends DoctrineEntityRepository implements SponsorRepository
{

    public function aSponsorInProgram(Program $program, string $sponsorId): Sponsor
    {
        $params = [
            'programId' => $program->getId(),
            'sponsorId' => $sponsorId,
        ];
        $qb = $this->createQueryBuilder('sponsor');
        $qb->select('sponsor')
                ->andWhere($qb->expr()->eq('sponsor.id', ':sponsorId'))
                ->leftJoin('sponsor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setMaxResults(1)
                ->setParameters($params);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound("not found: sponsor not found");
        }
    }

    public function allSponsorsInProgram(Program $program, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            'programId' => $program->getId(),
        ];
        $qb = $this->createQueryBuilder('sponsor');
        $qb->select('sponsor')
                ->leftJoin('sponsor.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq('sponsor.disabled', ":activeStatus"))
                    ->setParameter("activeStatus", !$activeStatus);
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
