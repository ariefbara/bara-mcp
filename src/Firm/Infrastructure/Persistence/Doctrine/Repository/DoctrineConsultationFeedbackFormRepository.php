<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\ConsultationFeedbackFormRepository,
    Domain\Model\Firm\ConsultationFeedbackForm
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder,
    Uuid
};

class DoctrineConsultationFeedbackFormRepository extends EntityRepository implements ConsultationFeedbackFormRepository
{

    public function add(ConsultationFeedbackForm $consultationFeedbackForm): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationFeedbackForm);
        $em->flush();
    }

    public function all(string $firmId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
        ];
        $qb = $this->createQueryBuilder('consultationFeedbackForm');
        $qb->select('consultationFeedbackForm')
                ->andWhere($qb->expr()->eq('consultationFeedbackForm.removed', "false"))
                ->leftJoin('consultationFeedbackForm.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $consultationFeedbackFormId): ConsultationFeedbackForm
    {
        $params = [
            "firmId" => $firmId,
            "consultationFeedbackFormId" => $consultationFeedbackFormId,
        ];
        $qb = $this->createQueryBuilder('consultationFeedbackForm');
        $qb->select('consultationFeedbackForm')
                ->andWhere($qb->expr()->eq('consultationFeedbackForm.id', ":consultationFeedbackFormId"))
                ->andWhere($qb->expr()->eq('consultationFeedbackForm.removed', "false"))
                ->leftJoin('consultationFeedbackForm.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation feedback form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
