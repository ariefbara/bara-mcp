<?php

namespace Resources;

use ReflectionClass;
use Resources\Exception\RegularException;

abstract class BaseEnum
{

    protected $value;

    public function __construct($value)
    {
        $c = new ReflectionClass($this);
        if (!in_array($value, $c->getConstants())) {
            throw RegularException::badRequest('bad request: invalid enum argument');
        }
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

}
