<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission;

class LearningMaterial
{

    /**
     *
     * @var Mission
     */
    protected $mission;

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
     * @var string
     */
    protected $content;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getMission(): Mission
    {
        return $this->mission;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): ?string
    {
        return $this->name;
    }

    function getContent(): ?string
    {
        return $this->content;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function __construct(Mission $mission, string $id, string $name, string $content)
    {
        $this->mission = $mission;
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->removed = false;
    }

    public function update(string $name, string $content): void
    {
        $this->name = $name;
        $this->content = $content;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
