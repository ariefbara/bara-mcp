<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Auth\Firm\Program\ConsultantRepository as InterfaceForAuthorization,
    Application\Service\Firm\Program\ConsultantRepository,
    Domain\Model\Firm\Program\Consultant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository, InterfaceForAuthorization
{

    public function aProgramConsultationOfPersonnel(string $firmId, string $personnelId, string $programConsultationId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programConsultationId' => $programConsultationId,
        ];

        $qb = $this->createQueryBuilder('programConsultation');
        $qb->select('programConsultation')
                ->andWhere($qb->expr()->eq('programConsultation.id', ':programConsultationId'))
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: program consultation not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allProgramConsultationOfPersonnel(string $firmId, string $personnelId, int $page, int $pageSize)
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('programConsultation');
        $qb->select('programConsultation')
                ->leftJoin('programConsultation.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfUnremovedConsultantCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'program_firm')
                ->andWhere($qb->expr()->eq('program_firm.id', ':firmId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'personel_firm')
                ->andWhere($qb->expr()->eq('personel_firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty(count($qb->getQuery()->getResult()));
    }

    public function ofId(string $firmId, string $programId, string $consultantId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'consultantId' => $consultantId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
