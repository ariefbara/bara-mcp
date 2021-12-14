<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\FeedbackFormRepository;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Task\Dependency\Firm\FeedbackFormRepository as FeedbackFormRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineFeedbackFormRepository extends DoctrineEntityRepository implements FeedbackFormRepository, FeedbackFormRepository2
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

    public function aFeedbackFormOfId(string $id): FeedbackForm
    {
        return $this->findOneByIdOrDie($id, 'feedback form');
    }

}
