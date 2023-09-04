<?php

namespace Bara\Domain\Model;

use Bara\Domain\Model\Firm\Manager;
use Bara\Domain\Model\Firm\ManagerData;
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;

class Firm extends EntityContainEvents
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
    protected $name;

    /**
     *
     * @var string
     */
    protected $identifier;

    /**
     *
     * @var FirmWhitelableInfo
     */
    protected $firmWhitelableInfo;

    /**
     * 
     * @var float||null
     */
    protected $sharingPercentage = 0;

    /**
     *
     * @var bool
     */
    protected $suspended = false;

    /**
     *
     * @var ArrayCollection
     */
    protected $managers;

    private function setName(string $name): void
    {
        $errorDetail = 'bad request: firm name is required';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    private function setIdentifier(string $identifier): void
    {
        $errorDetail = 'bad request: firm identifier is required and must only contain alphanumeric, underscore '
                . 'and hypen character without whitespace';
        ValidationService::build()
                ->addRule(ValidationRule::alphanumeric("-_"))
                ->addRule(ValidationRule::noWhitespace())
                ->execute($identifier, $errorDetail);
        $this->identifier = $identifier;
    }

    protected function setSharingPercentage(?float $sharingPercentage): void
    {
        $errorDetail = 'sharing percentage must be valid percentage value';
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::between(0, 100)))
                ->execute($sharingPercentage, $errorDetail);
        $this->sharingPercentage = $sharingPercentage;
    }

    function __construct(string $id, FirmData $firmData, ManagerData $managerData)
    {
        $this->id = $id;
        $this->setName($firmData->getName());
        $this->setIdentifier($firmData->getIdentifier());
        $this->firmWhitelableInfo = new FirmWhitelableInfo(
                $firmData->getWhitelableUrl(), $firmData->getWhitelableMailSenderAddress(),
                $firmData->getWhitelableMailSenderName());
        $this->setSharingPercentage($firmData->getSharingPercentage());
        $this->suspended = false;

        $this->managers = new ArrayCollection();
        $managerId = Uuid::generateUuid4();
        $manager = new Manager($this, $managerId, $managerData);
        $this->managers->add($manager);
        //
        $this->recordEvent(new \Bara\Domain\Event\FirmCreatedEvent($this->id, $this->identifier));
    }

    public function suspend(): void
    {
        $this->suspended = true;
    }

}
