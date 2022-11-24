<?php

namespace SharedContext\Domain\ValueObject;

use Resources\ValidationRule;
use Resources\ValidationService;

class Label
{

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $description;

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    protected function setName(string $name): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, 'bad request: name is mandatory');
        $this->name = $name;
    }

    public function __construct(LabelData $labelData)
    {
        $this->setName($labelData->getName());
        $this->description = $labelData->getDescription();
    }
    
    public function update(LabelData $data): self
    {
        return new static($data);
    }
    
    public function sameValueAs(Label $other): bool
    {
        return $this->name == $other->name && $this->description == $other->description;
    }

}
