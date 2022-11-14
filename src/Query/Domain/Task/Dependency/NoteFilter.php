<?php

namespace Query\Domain\Task\Dependency;

use Resources\PaginationFilter;
use Resources\QueryOrder;

class NoteFilter
{

    /**
     * 
     * @var QueryOrder|null
     */
    protected $modifiedTimeOrder;

    /**
     * 
     * @var QueryOrder|null
     */
    protected $createdTimeOrder;

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    public function setModifiedTimeOrder(?QueryOrder $modifiedTimeOrder)
    {
        $this->modifiedTimeOrder = $modifiedTimeOrder;
        return $this;
    }

    public function setCreatedTimeOrder(?QueryOrder $createdTimeOrder)
    {
        $this->createdTimeOrder = $createdTimeOrder;
        return $this;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }

    public function getModifiedTimeOrder(): ?QueryOrder
    {
        return $this->modifiedTimeOrder;
    }

    public function getCreatedTimeOrder(): ?QueryOrder
    {
        return $this->createdTimeOrder;
    }

    public function getPaginationFilter(): PaginationFilter
    {
        return $this->paginationFilter;
    }

    //
    public function getOrderStatement(string $modifiedTimeColumName, string $createdTimeColumnName): string
    {
        $orders = [];
        if(!empty($this->modifiedTimeOrder)){
            $orders[] = "{$modifiedTimeColumName} {$this->modifiedTimeOrder->getOrder()}";
        }
        if(!empty($this->createdTimeOrder)) {
            $orders[] = "{$createdTimeColumnName} {$this->createdTimeOrder->getOrder()}";
        }
        $orderStatement = implode(', ', $orders);
        return empty($orderStatement) ? '' : "ORDER BY {$orderStatement}";
    }

}
