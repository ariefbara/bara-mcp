<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program;
use Resources\Exception\RegularException;
use Resources\ValidationRule;
use Resources\ValidationService;

class Sponsor
{

    /**
     * 
     * @var Program
     */
    protected $program;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var FirmFileInfo|null
     */
    protected $logo;

    /**
     * 
     * @var string|null
     */
    protected $website;

    protected function setName(?string $name): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, "bad request: name is mandatory");
        $this->name = $name;
    }

    protected function setWebsite(?string $website): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::domain()))
                ->execute($website, 'bad request: website must be a valid domain format');
        $this->website = $website;
    }

    protected function setLogo(?FirmFileInfo $logo): void
    {
        if (isset($logo)) {
            $this->program->assertFileUsable($logo);
        }
        $this->logo = $logo;
    }

    public function __construct(Program $program, string $id, SponsorData $sponsorData)
    {
        $this->program = $program;
        $this->id = $id;
        $this->disabled = false;
        $this->setName($sponsorData->getName());
        $this->setWebsite($sponsorData->getWebsite());
        $this->setLogo($sponsorData->getLogo());
    }

    public function update(SponsorData $sponsorData): void
    {
        $this->setName($sponsorData->getName());
        $this->setWebsite($sponsorData->getWebsite());
        $this->setLogo($sponsorData->getLogo());
    }

    public function disable(): void
    {
        $this->disabled = true;
    }

    public function enable(): void
    {
        $this->disabled = false;
    }

    public function assertManageableInProgram(Program $program): void
    {
        if ($this->program !== $program) {
            throw RegularException::forbidden("forbidden: unable to manage sponsor, probably belongs to other program");
        }
    }

}
