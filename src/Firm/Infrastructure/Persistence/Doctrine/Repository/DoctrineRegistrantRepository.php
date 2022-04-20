<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\Program\RegistrantRepository;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Task\Dependency\Firm\Program\RegistrantRepository as RegistrantRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineRegistrantRepository extends DoctrineEntityRepository implements RegistrantRepository, RegistrantRepository2
{

    public function ofId(string $firmId, string $programId, string $registrantId): Registrant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'registrantId' => $registrantId,
        ];

        $qb = $this->createQueryBuilder('registrant');
        $qb->select('registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ':registrantId'))
                ->leftJoin('registrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: registrant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aRegistrantOfId(string $id): Registrant
    {
        return $this->findOneByIdOrDie($id, 'registrant');
    }

}
