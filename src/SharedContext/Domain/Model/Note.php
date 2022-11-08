<?php

namespace SharedContext\Domain\Model;

use DateTimeImmutable;
use Resources\DateTimeImmutableBuilder;
use Resources\ValidationRule;
use Resources\ValidationService;

class Note
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $content;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $createdTime;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $removed;

    protected function setContent(string $content)
    {
        
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($content, 'note content is required');
        $this->content = $content;
    }

    public function __construct(string $id, string $content)
    {
        $this->id = $id;
        $this->setContent($content);
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->modifiedTime = $this->createdTime;
        $this->removed = false;
    }

    public function update(string $content): void
    {
        if ($this->content !== $content) {
            $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
        $this->setContent($content);
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
