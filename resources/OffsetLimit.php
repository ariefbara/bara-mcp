<?php

namespace Resources;

use Resources\OffsetLimit\Order;

class OffsetLimit
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
     * @var Order[]
     */
    protected $orders;

    public function __construct(int $page = 1, int $pageSize = 25)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }
    
    public function addOrder(Order $order): self
    {
        $this->orders[$order->getTableColumnName()] = $order;
        return $this;
    }

    public function geLimitStatement(): string
    {
        $offset = $this->pageSize * ($this->page - 1);
        return "LIMIT {$offset}, {$this->pageSize}";
    }

    public function geOrderStatement(): string
    {
        $orderStatements = '';
        if (empty($this->orders)) {
            $orderStatements = "ORDER BY id ASC";
        }
        foreach ($this->orders as $order) {
            $orderStatements .= empty($orderStatements) ?
                    "ORDER BY {$order->getOrderStatement()}" : ", {$order->getOrderStatement()}";
        }
        return $orderStatements;
    }

}
