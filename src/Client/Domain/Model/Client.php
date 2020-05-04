<?php

namespace Client\Domain\Model;

use Client\Domain\{
    Event\ClientActivationCodeGenerated,
    Event\ClientPasswordResetCodeGenerated,
    Model\Client\ClientNotification,
    Model\Client\ProgramRegistration,
    Model\Firm\Program
};
use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Resources\{
    Domain\Model\ModelContainEvents,
    Domain\ValueObject\Password,
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};
use Shared\Domain\Model\Notification;

class Client extends ModelContainEvents
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
    protected $email;

    /**
     *
     * @var Password
     */
    protected $password;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $signupTime;

    /**
     *
     * @var string
     */
    protected $activationCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $activationCodeExpiredTime = null;

    /**
     *
     * @var string
     */
    protected $resetPasswordCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $resetPasswordCodeExpiredTime = null;

    /**
     *
     * @var bool
     */
    protected $activated = false;

    /**
     *
     * @var ArrayCollection
     */
    protected $programRegistrations;

    /**
     *
     * @var ArrayCollection
     */
    protected $programParticipations;

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    private function setName($name)
    {
        $errorDetail = "bad request: client name is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    private function setEmail($email)
    {
        $errorDetail = "bad request: client email is required and must be in valid email address format";
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    function __construct(string $id, string $name, string $email, string $password)
    {
        $this->id = $id;
        $this->setName($name);
        $this->setEmail($email);
        $this->password = new Password($password);
        $this->signupTime = new DateTimeImmutable();
        $this->activated = false;

        $this->generateActivationCode();
    }

    public function generateResetPasswordCode(): void
    {
        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = new DateTimeImmutable("+24 hours");
        $event = new ClientPasswordResetCodeGenerated($this->name, $this->email, $this->resetPasswordCode);
        $this->recordEvent($event);
    }

    public function generateActivationCode(): void
    {
        if ($this->activated) {
            $errorDetails = 'forbidden: account already activated';
            throw RegularException::forbidden($errorDetails);
        }
        $this->activationCode = bin2hex(random_bytes(32));
        $this->activationCodeExpiredTime = new DateTimeImmutable('+24 hours');

        $event = new ClientActivationCodeGenerated($this->name, $this->email, $this->activationCode);
        $this->recordEvent($event);
    }

    public function activate(string $activationCode): void
    {
        if ($this->activated) {
            $errorDetail = 'forbidden: account already activated';
            throw RegularException::forbidden($errorDetail);
        }
        $this->assertValidToActivate($activationCode);
        $this->activated = true;
        $this->activationCode = null;
        $this->activationCodeExpiredTime = null;
    }

    protected function assertValidToActivate(string $activationCode): void
    {
        if (
                empty($this->activationCode) ||
                $this->activationCode != $activationCode ||
                $this->activationCodeExpiredTime < new DateTimeImmutable()
        ) {
            $errorDetail = 'bad request: invalid or expired token';
            throw RegularException::badRequest($errorDetail);
        }
    }

    public function resetPassword(string $resetPasswordCode, string $password): void
    {
        $this->assertValidToResetPassword($resetPasswordCode);
        $this->password = new Password($password);
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
    }

    protected function assertValidToResetPassword(string $resetPasswordCode): void
    {
        if (
                empty($this->resetPasswordCode) ||
                $this->resetPasswordCode != $resetPasswordCode ||
                $this->resetPasswordCodeExpiredTime < new DateTimeImmutable()
        ) {
            $errorDetails = 'bad request: invalid or expired token';
            throw RegularException::badRequest($errorDetails);
        }
    }

    public function changeProfile(string $name): void
    {
        $this->setName($name);
    }

    public function changePassword(string $previousPassword, string $newPassword): void
    {
        if (!$this->password->match($previousPassword)) {
            $errorDetails = "forbidden: previous password not match";
            throw RegularException::forbidden($errorDetails);
        }
        $this->password = new Password($newPassword);
    }

    public function emailEquals(string $email): bool
    {
        return strcasecmp($this->email, $email) == 0;
    }

    public function createProgramRegistration(string $id, Program $program): ProgramRegistration
    {
        $this->assertNoUnconcludedRegistrationInProgram($program);
        $this->assertNoActiveParticipantionInProgram($program);
        return new ProgramRegistration($this, $id, $program);
    }

    protected function assertNoUnconcludedRegistrationInProgram(Program $program): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('program', $program))
                ->andWhere(Criteria::expr()->eq('concluded', false));
        if (!empty($this->programRegistrations->matching($criteria)->count())) {
            $errorDetail = "forbidden: you already registered to this program";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNoActiveParticipantionInProgram(Program $program): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('program', $program))
                ->andWhere(Criteria::expr()->eq('active', true));
        if (!empty($this->programParticipations->matching($criteria)->count())) {
            $errorDetail = "forbidden: you already participate in this program";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
