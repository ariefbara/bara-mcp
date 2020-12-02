<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\{
    Application\Service\Firm\FeedbackFormRepository,
    Application\Service\Manager\FeedbackFormRepository as InterfaceForManager,
    Domain\Model\Firm\FeedbackForm,
    Domain\Service\FeedbackFormRepository as InterfaceForDomainService
};
use Resources\{
    Exception\RegularException,
    Uuid
};

class DoctrineFeedbackFormRepository extends EntityRepository implements FeedbackFormRepository, InterfaceForDomainService,
        InterfaceForManager
{

    public function add(FeedbackForm $feedbackForm): void
    {
        $em = $this->getEntityManager();
        $em->persist($feedbackForm);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
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
            $errorDetail = "not found: consultation feedback form not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aFeedbackFormOfId(string $feedbackFormId): FeedbackForm
    {
        $feedbackForm = $this->findOneBy(["id" => $feedbackFormId]);
        if (empty($feedbackForm)) {
            $errorDetail = "not found: feedback form not found";
            throw RegularException::notFound($errorDetail);
        }
        return $feedbackForm;
    }

}
