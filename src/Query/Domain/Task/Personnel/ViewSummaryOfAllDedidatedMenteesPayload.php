<?php

namespace Query\Domain\Task\Personnel;

use Resources\Domain\ValueObject\QueryOrder;

class ViewSummaryOfAllDedidatedMenteesPayload
{

    /**
     * 
     * @var int
     */
    protected $page;

    /**
     * 
     * @var int
     */
    protected $pageSize;

    /**
     * 
     * @var QueryOrder
     */
    protected $queryOrder;
    public $result;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getQueryOrder(): QueryOrder
    {
        return $this->queryOrder;
    }

    public function __construct(int $page, int $pageSize, QueryOrder $queryOrder)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->queryOrder = $queryOrder;
    }

}
