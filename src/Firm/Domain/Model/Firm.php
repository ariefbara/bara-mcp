<?php

namespace Firm\Domain\Model;

use Firm\Domain\{
    Event\ClientSignupAcceptedEvent,
    Model\Firm\Client,
    Model\Firm\ClientData
};
use Query\Domain\Model\FirmWhitelableInfo;
use Resources\{
    Domain\Model\ModelContainEvents,
    Exception\RegularException
};

class Firm extends ModelContainEvents
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

    function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->firmWhitelableInfo;
    }

    protected function __construct()
    {
        
    }

    public function acceptClientSignup(string $clientId, ClientData $clientData): Client
    {
        if ($this->suspended) {
            $errorDetail = 'forbidden: unable to signup to suspended firm';
            throw RegularException::forbidden($errorDetail);
        }

        $event = new ClientSignupAcceptedEvent($this->id, $clientId);
        $this->recordEvent($event);

        return new Client($this, $clientId, $clientData);
    }

    public function getWhitelableUrl(): string
    {
        return $this->firmWhitelableInfo->getUrl();
    }

    public function getWhitelableMailSenderAddress(): string
    {
        return $this->firmWhitelableInfo->getMailSenderAddress();
    }

    public function getWhitelableMailSenderName(): string
    {
        return $this->firmWhitelableInfo->getMailSenderName();
    }

}
