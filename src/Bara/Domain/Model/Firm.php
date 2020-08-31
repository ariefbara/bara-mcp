<?php

namespace Bara\Domain\Model;

use Bara\Domain\Model\Firm\{
    Manager,
    ManagerData
};
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\{
    Uuid,
    ValidationRule,
    ValidationService
};

class Firm
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

    function __construct(string $id, FirmData $firmData, ManagerData $managerData)
    {
        $this->id = $id;
        $this->setName($firmData->getName());
        $this->setIdentifier($firmData->getIdentifier());
        $this->firmWhitelableInfo = new FirmWhitelableInfo(
                $firmData->getWhitelableUrl(), $firmData->getWhitelableMailSenderAddress(),
                $firmData->getWhitelableMailSenderName());
        $this->suspended = false;

        $this->managers = new ArrayCollection();
        $managerId = Uuid::generateUuid4();
        $manager = new Manager($this, $managerId, $managerData);
        $this->managers->add($manager);
    }

    public function suspend(): void
    {
        $this->suspended = true;
    }

}
