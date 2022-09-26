<?php

namespace Query\Domain\Task;

class CommonViewDetailPayload
{

    /**
     * 
     * @var string
     */
    protected $id;
    public $result;

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(string $id)
    {
        $this->id = $id;
    }

}
