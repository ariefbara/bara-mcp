<?php

namespace Query\Domain\Task\Dependency;

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

    public function __construct()
    {
        
    }

    public function getModifiedTimeOrder(): ?QueryOrder
    {
        return $this->modifiedTimeOrder;
    }

    public function getCreatedTimeOrder(): ?QueryOrder
    {
        return $this->createdTimeOrder;
    }

}
