<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Listener\ClientRegistrantRepository as ClientRegistrantRepository2;
use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Firm\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use Firm\Domain\Task\Dependency\Firm\Client\ClientRegistrantRepository;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineClientRegistrantRepository extends DoctrineEntityRepository implements ClientRegistrantRepository, ClientRegistrantRepository2
{

    public function ofId(string $id): ClientRegistrant
    {
        return $this->findOneByIdOrDie($id, 'client registrant');
    }

    public function aClientRegistrantOwningInvoiceId(string $invoiceId): ?ClientRegistrant
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

        $qb = $this->createQueryBuilder('clientRegistrant');
        $qb->select('clientRegistrant')
                ->leftJoin('clientRegistrant.registrant', 'registrant')
                ->andWhere($qb->expr()->in('registrant.id', $registrantQb->getDQL()))
                ->setMaxResults(1)
                ->setParameters($params);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

    public function ofRegistrantIdOrNull(string $registrantId): ?ClientRegistrant
    {
        $qb = $this->createQueryBuilder('clientRegistrant');
        $qb->select('clientRegistrant')
                ->leftJoin('clientRegistrant.registrant', 'registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ':registrantId'))
                ->setParameter('registrantId', $registrantId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

}
