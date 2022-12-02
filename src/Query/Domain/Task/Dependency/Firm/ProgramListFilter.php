<?php

namespace Query\Domain\Task\Dependency\Firm;

use Doctrine\ORM\QueryBuilder;
use Resources\PaginationFilter;

class ProgramListFilter
{

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var string|null
     */
    protected $firmId;

    /**
     * 
     * @var bool|null
     */
    protected $publishedStatus;

    public function setPublishedStatus(?bool $publishedStatus)
    {
        $this->publishedStatus = $publishedStatus;
        return $this;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }

    public function getPaginationFilter(): PaginationFilter
    {
        return $this->paginationFilter;
    }

    public function applyCriteriaToDoctrineQueryBuilder(QueryBuilder $qb): void
    {
        if (!empty($this->firmId)) {
            $qb->leftJoin('program.firm', 'firm')
                    ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                    ->setParameter('firmId', $this->firmId);
        }

        if (!is_null($this->publishedStatus)) {
            $qb->andWhere($qb->expr()->eq('program.published', ':publishedStatus'))
                    ->setParameter('publishedStatus', $this->publishedStatus);
        }
    }

}
