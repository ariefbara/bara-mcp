<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Mission;

use Query\Domain\Task\PaginationPayload;

class MissionCommentFilter
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var PaginationPayload|null
     */
    protected $pagination;

    /**
     * 
     * @var string|null
     */
    protected $order = self::DESC;

    public function getPagination(): ?PaginationPayload
    {
        return $this->pagination;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function __construct(?PaginationPayload $pagination)
    {
        $this->pagination = $pagination;
    }

    public function setOrder(?string $order): self
    {
        if (isset($order)) {
            $c = new \ReflectionClass($this);
            if (!in_array($order, $c->getConstants())) {
                $path = explode('\\', static::class);
                $className = array_pop($path);
                throw RegularException::badRequest("bad request: invalid value for mission comment order");
            }
            $this->order = $order;
        }
        return $this;
    }

}
