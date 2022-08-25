<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Listener\TeamRegistrantRepository;
use Firm\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineTeamRegistrantRepository extends DoctrineEntityRepository implements TeamRegistrantRepository
{
    
    public function ofRegistrantIdOrNull(string $registrantId): ?TeamRegistrant
    {
        $qb = $this->createQueryBuilder('teamRegistrant');
        $qb->select('teamRegistrant')
                ->leftJoin('teamRegistrant.registrant', 'registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ':registrantId'))
                ->setParameter('registrantId', $registrantId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

    public function aTeamRegistrantOwningInvoiceId(string $invoiceId): ?TeamRegistrant
    {
        $params = [
            'invoiceId' => $invoiceId,
        ];

        $registrantQb = $this->getEntityManager()->createQueryBuilder();
        $registrantQb->select('a_registrant.id')
                ->from(RegistrantInvoice::class, 'a_registrantInvoice')
                ->leftJoin('a_registrantInvoice.registrant', 'a_registrant')
                ->leftJoin('a_registrantInvoice.invoice', 'a_invoice')
                ->andWhere($registrantQb->expr()->eq('a_invoice', ':invoiceId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('teamRegistrant');
        $qb->select('teamRegistrant')
                ->leftJoin('teamRegistrant.registrant', 'registrant')
                ->andWhere($qb->expr()->in('registrant.id', $registrantQb->getDQL()))
                ->setMaxResults(1)
                ->setParameters($params);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

}
