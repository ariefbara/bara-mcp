<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Firm\Manager\ManagerInvitationRepository;
use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineManagerInviteeRepository extends EntityRepository implements ManagerInvitationRepository
{
    
    protected function applyFilter(QueryBuilder $qb, ?InviteeFilter $inviteeFilter): void
    {
        if (!isset($inviteeFilter)) {
            return;
        }
        $from = $inviteeFilter->getFrom();
        $to = $inviteeFilter->getTo();
        if (isset($from)) {
            $qb->andWhere($qb->expr()->gte('activity.startEndTime.startDateTime', ':from'))
                    ->setParameter('from', $from);
        }
        if (isset($to)) {
            $qb->andWhere($qb->expr()->lte('activity.startEndTime.startDateTime', ':to'))
                    ->setParameter('to', $to);
        }
        $cancelledStatus = $inviteeFilter->getCancelledStatus();
        if (isset($cancelledStatus)) {
            $qb->andWhere($qb->expr()->eq('invitee.cancelled', ':cancelled'))
                    ->setParameter('cancelled', $cancelledStatus);
        }
        if (!empty($inviteeFilter->getWillAttendStatuses())) {
            $orX = $qb->expr()->orX();
            foreach ($inviteeFilter->getWillAttendStatuses() as $willAttendStatus) {
                if (is_null($willAttendStatus)) {
                    $orX->add($qb->expr()->isNull('invitee.willAttend'));
                } else {
                    $orX->add($qb->expr()->eq('invitee.willAttend', ':willAttendStatus'));
                    $qb->setParameter('willAttendStatus', $willAttendStatus);
                }
            }
            $qb->andWhere($orX);
        }
        $order = $inviteeFilter->getOrder();
        if (!empty($order)) {
            $qb->orderBy('activity.startEndTime.startDateTime', $order);
        }
    }

    public function allInvitationsForManager(
            string $firmId, string $managerId, int $page, int $pageSize, ?InviteeFilter $inviteeFilter)
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder("managerInvitation");
        $qb->select("managerInvitation")
                ->leftJoin("managerInvitation.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->leftJoin('managerInvitation.invitee', 'invitee')
                ->leftJoin('invitee.activity', 'activity')
                ->orderBy('activity.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);
        
        $this->applyFilter($qb, $inviteeFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForManager(string $firmId, string $managerId, string $invitationId): ManagerInvitee
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "managerInvitationId" => $invitationId,
        ];

        $qb = $this->createQueryBuilder("managerInvitation");
        $qb->select("managerInvitation")
                ->andWhere($qb->expr()->eq("managerInvitation.id", ":managerInvitationId"))
                ->leftJoin("managerInvitation.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
