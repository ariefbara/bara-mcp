<?php

namespace Resources\Domain\ValueObject;

use Resources\BaseEnum;

class QueryOrder extends BaseEnum
{

    const ASCENDING = 'ASC';
    const DESCENDING = 'DESC';

    public function __construct($value = self::ASCENDING)
    {
        parent::__construct($value);
    }

}
