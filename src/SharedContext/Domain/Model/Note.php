<?php

namespace SharedContext\Domain\Model;

use DateTimeImmutable;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;

class Note
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Label
     */
    protected $label;

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

    public function __construct(string $id, LabelData $labelData)
    {
        $this->id = $id;
        $this->label = new Label($labelData);
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->modifiedTime = $this->createdTime;
        $this->removed = false;
    }

    public function update(LabelData $labelData): void
    {
        $previousLabel = $this->label;
        $this->label = $this->label->update($labelData);
        if (!$this->label->sameValueAs($previousLabel)) {
            $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
