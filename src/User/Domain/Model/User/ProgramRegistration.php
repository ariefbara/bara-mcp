<?php

namespace User\Domain\Model\User;

use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\Exception\RegularException;
use User\Domain\Model\ {
    ProgramInterface,
    User
};

class ProgramRegistration
{

    /**
     *
     * @var User
     */
    protected $user;

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

    public function __construct(User $user, string $id, ProgramInterface $program)
    {
        if (!$program->isRegistrationOpenFor(ParticipantTypes::USER_TYPE)) {
            $errorDetail = 'forbidden: program registration closed';
            throw RegularException::forbidden($errorDetail);
        }
        
        $this->user = $user;
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
