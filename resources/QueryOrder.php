<?php

namespace Resources;

class QueryOrder
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var string
     */
    protected $order;

    public function __construct($order = self::ASC)
    {
        if (!in_array($order, [self::ASC, self::DESC])) {
            throw RegularException::badRequest('bad request: invalid order argument');
        }
        $this->order = $order;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

}
