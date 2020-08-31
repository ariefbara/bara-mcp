<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\{
    Client,
    ProgramInterface
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Exception\RegularException;

class ProgramRegistration
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Registrant
     */
    protected $registrant;

    public function __construct(Client $client, string $id, ProgramInterface $program)
    {
        if (!$program->isRegistrationOpenFor(ParticipantTypes::CLIENT_TYPE)) {
            $errorDetail = 'forbidden: program registration is closed';
            throw RegularException::forbidden($errorDetail);
        }

        $this->client = $client;
        $this->id = $id;
        $this->programId = $program->getId();
        $this->registrant = new Registrant($program, $id);
    }

    public function cancel(): void
    {
        $this->registrant->cancel();
    }

    public function isUnconcludedRegistrationToProgram(ProgramInterface $program): bool
    {
        return $this->registrant->isUnconcludedRegistrationToProgram($program);
    }

}
