<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;

class Task
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

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

    public function __construct(Participant $participant, string $id, LabelData $data)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->label = new Label($data);
        $this->cancelled = false;
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public function update(LabelData $data): void
    {
        $previousLabel = $this->label;
        $this->label = $this->label->update($data);
        if (!$previousLabel->sameValueAs($this->label)) {
            $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        }
    }

    public function cancel(): void
    {
        $this->cancelled = true;
    }

}
