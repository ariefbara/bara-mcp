<?php
namespace Resources\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;

class PaginatorBuilder
{

    /**
     * 
     * @param Query $query
     * @param int $page
     * @param int $pageSize
     * @param boolean $fetchJoinCollection
     * this must be set false for querying entity with composite primary key (primary key more than one column)
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public static function build(Query $query, int $page, int $pageSize, $fetchJoinCollection = true)
    {
        $paginator = new Paginator($query, $fetchJoinCollection);
        $paginator->getQuery()
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);
        return $paginator;
    }
}

