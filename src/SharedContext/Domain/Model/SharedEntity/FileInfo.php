<?php

namespace SharedContext\Domain\Model\SharedEntity;

use Resources\{
    ValidationRule,
    ValidationService
};

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

    protected function setName(string $name): void
    {
        $regex = "/^[\w\s-]+\.[a-zA-Z0-9]{2,4}$/";
        $errorDetails = "bad request: file name is required and must include extension";
        ValidationService::build()
                ->addRule(ValidationRule::regex($regex))
                ->execute($name, $errorDetails);

        $this->name = $name;
    }

    protected function setFolders(array $folders): void
    {
        foreach ($folders as $folder) {
            $sanitizedFolder = preg_replace('/[^A-Za-z0-9 _-]/', '', $folder);
            $this->folders[] = $sanitizedFolder;
        }
    }

    public function __construct(string $id, FileInfoData $fileInfoData)
    {
        $this->id = $id;
        $this->setFolders($fileInfoData->getFolders());
        $this->setName($fileInfoData->getName());
        $this->size = $fileInfoData->getSize();
    }

    public function getFullyQualifiedFileName(): string
    {
        $path = '';
        foreach ($this->folders as $folder) {
            $path .= DIRECTORY_SEPARATOR . $folder;
        }
        return $path . DIRECTORY_SEPARATOR . $this->name;
    }

    public function updateSize(?float $size): void
    {
        $this->size = $size;
    }

}
