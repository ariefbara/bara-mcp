<?php

namespace SharedContext\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;
use SharedContext\ {
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\Firm\Program\ConsultationSetup
};

class DoctrineConsultationSetupRepository extends EntityRepository implements ConsultationSetupRepository
{

    public function ofId(string $firmId, string $programId, string $consultationSetupId): ConsultationSetup
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'consultationSetupId' => $consultationSetupId,
        ];

        $qb = $this->createQueryBuilder('consultationSetup');
        $qb->select('consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->leftJoin('consultationSetup.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->andWhere($qb->expr()->eq('program.firmId', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation setup not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
