<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor\DedicatedMentorRepository;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineDedicatedMentorRepository extends DoctrineEntityRepository implements DedicatedMentorRepository
{
    
    public function aDedicatedMentorBelongsToPersonnel(
            string $firmId, string $personnelId, string $dedicatedMentorId): DedicatedMentor
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'dedicatedMentorId' => $dedicatedMentorId,
        ];
        
        $qb = $this->createQueryBuilder('dedicatedMentor');
        $qb->select('dedicatedMentor')
                ->andWhere($qb->expr()->eq('dedicatedMentor.id', ':dedicatedMentorId'))
                ->leftJoin('dedicatedMentor.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->andWhere($qb->expr()->eq('personnel.firmId', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: dedicated mentor not found');
        }
    }

}
