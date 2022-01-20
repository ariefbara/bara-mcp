<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;

class Section
{

    /**
     * 
     * @var Form
     */
    protected $form;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $position;

    /**
     * 
     * @var bool
     */
    protected $removed;

    public function getId(): string
    {
        return $this->id;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function __construct(Form $form, string $id, SectionData $sectionData)
    {
        $this->form = $form;
        $this->id = $id;
        $this->name = $sectionData->getName();
        $this->position = $sectionData->getPosition();
        $this->removed = false;
    }
    
    public function update(SectionData $sectionData): void
    {
        $this->name = $sectionData->getName();
        $this->position = $sectionData->getPosition();
    }
    
    public function remove(): void
    {
        $this->removed = true;
    }

}
