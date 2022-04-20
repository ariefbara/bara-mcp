<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Firm\Application\Service\GenericRepository;

class DoctrineGenericRepository implements GenericRepository
{

    /**
     * 
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function update(): void
    {
        $this->em->flush();
    }

}
