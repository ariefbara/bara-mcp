<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Query\Application\Service\Firm\Personnel\ProgramConsultant\ConsultantInvitationRepository;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantInviteeFilter;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantInviteeRepository;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineConsultantInviteeRepository extends EntityRepository implements ConsultantInvitationRepository, ConsultantInviteeRepository
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

    public function allInvitationsForConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize,
            ?InviteeFilter $inviteeFilter)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "consultantId" => $consultantId,
        ];

        $qb = $this->createQueryBuilder("consultantInvitation");
        $qb->select("consultantInvitation")
                ->leftJoin("consultantInvitation.consultant", "consultant")
                ->andWhere($qb->expr()->eq("consultant.id", ":consultantId"))
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->leftJoin('consultantInvitation.invitee', 'invitee')
                ->leftJoin('invitee.activity', 'activity')
                ->orderBy('activity.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);

        $this->applyFilter($qb, $inviteeFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForConsultant(string $firmId, string $personnelId, string $invitationId): ConsultantInvitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];

        $qb = $this->createQueryBuilder("consultantInvitation");
        $qb->select("consultantInvitation")
                ->andWhere($qb->expr()->eq("consultantInvitation.id", ":invitationId"))
                ->leftJoin("consultantInvitation.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
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

    public function allInvitationWithPendingReportForPersonnel(
            string $personnelId, int $page, int $pageSize, ConsultantInviteeFilter $consultantInviteeFilter)
    {
        $params = [ 'personnelId' => $personnelId ];
        
        $inviteeReportQb = $this->getEntityManager()->createQueryBuilder();
        $inviteeReportQb->select('a_invitee.id')
                ->from(InviteeReport::class, 'a_inviteeReport')
                ->leftJoin('a_inviteeReport.invitee', 'a_invitee');
        
        $qb = $this->createQueryBuilder('consultantInvitee');
        $qb->select('consultantInvitee')
                ->leftJoin('consultantInvitee.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.active', 'true'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('consultantInvitee.invitee', 'invitee')
                ->andWhere($qb->expr()->notIn('invitee.id', $inviteeReportQb->getDQL()))
                ->andWhere($qb->expr()->eq('invitee.cancelled', 'false'))
                ->leftJoin('invitee.activity', 'activity')
                ->orderBy('activity.startEndTime.startDateTime', $consultantInviteeFilter->getQueryOrder()->getValue())
                ->setParameters($params);
        
        if ($consultantInviteeFilter->getFrom()) {
            $qb->andWhere($qb->expr()->gte('activity.startEndTime.startDateTime', ':from'))
                    ->setParameter('from', $consultantInviteeFilter->getFrom());
        }
        if ($consultantInviteeFilter->getTo()) {
            $qb->andWhere($qb->expr()->lte('activity.startEndTime.startDateTime', ':to'))
                    ->setParameter('to', $consultantInviteeFilter->getTo());
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
