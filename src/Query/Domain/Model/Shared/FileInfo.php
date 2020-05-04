<?php

namespace Query\Domain\Model\Shared;

class FileInfo
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var array
     */
    protected $folders = [];

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var float
     */
    protected $size = null;

    function getId(): string
    {
        return $this->id;
    }

    function getFolders(): array
    {
        return $this->folders;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getSize(): ?float
    {
        return $this->size;
    }

    protected function __construct()
    {
        
    }

    public function getFullyQualifiedFileName(): string
    {
        $path = '';
        foreach ($this->folders as $folder) {
            $path .= DIRECTORY_SEPARATOR . $folder;
        }
        return $path . DIRECTORY_SEPARATOR . $this->name;
    }

}
