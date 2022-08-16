<?php

namespace Payment\Infrastructure\Persistence\Doctrine\Repository;

use Payment\Application\Listener\ParticipantInvoiceRepository;
use Payment\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineParticipantInvoiceRepository extends DoctrineEntityRepository implements ParticipantInvoiceRepository
{
    
    public function ofId(string $participantInvoiceId): ?ParticipantInvoice
    {
        return $this->find($participantInvoiceId);
    }

}
