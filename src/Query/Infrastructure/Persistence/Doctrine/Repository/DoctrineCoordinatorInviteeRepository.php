<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Firm\Personnel\ProgramCoordinator\CoordinatorInvitationRepository;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineCoordinatorInviteeRepository extends EntityRepository implements CoordinatorInvitationRepository
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

    public function allInvitationsForCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize, 
            ?InviteeFilter $inviteeFilter)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorId" => $coordinatorId,
        ];

        $qb = $this->createQueryBuilder("coordinatorInvitation");
        $qb->select("coordinatorInvitation")
                ->leftJoin("coordinatorInvitation.coordinator", "coordinator")
                ->andWhere($qb->expr()->eq("coordinator.id", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->leftJoin('coordinatorInvitation.invitee', 'invitee')
                ->leftJoin('invitee.activity', 'activity')
                ->orderBy('activity.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);
        
        $this->applyFilter($qb, $inviteeFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForCoordinator(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];

        $qb = $this->createQueryBuilder("coordinatorInvitation");
        $qb->select("coordinatorInvitation")
                ->andWhere($qb->expr()->eq("coordinatorInvitation.id", ":invitationId"))
                ->leftJoin("coordinatorInvitation.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
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
