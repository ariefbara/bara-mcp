<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\FeedbackFormRepository,
    Domain\Model\Firm\FeedbackForm
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineFeedbackFormRepository extends EntityRepository implements FeedbackFormRepository
{

    public function all(string $firmId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
        ];
        $qb = $this->createQueryBuilder('feedbackForm');
        $qb->select('feedbackForm')
                ->andWhere($qb->expr()->eq('feedbackForm.removed', "false"))
                ->leftJoin('feedbackForm.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $feedbackFormId): FeedbackForm
    {
        $params = [
            "firmId" => $firmId,
            "feedbackFormId" => $feedbackFormId,
        ];
        $qb = $this->createQueryBuilder('feedbackForm');
        $qb->select('feedbackForm')
                ->andWhere($qb->expr()->eq('feedbackForm.id', ":feedbackFormId"))
                ->andWhere($qb->expr()->eq('feedbackForm.removed', "false"))
                ->leftJoin('feedbackForm.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: feedback form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
