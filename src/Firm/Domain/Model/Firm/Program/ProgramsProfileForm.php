<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ProfileForm;
use Firm\Domain\Model\Firm\Program;
use Resources\Exception\RegularException;

class ProgramsProfileForm implements AssetBelongsToFirm
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
     * @var ProfileForm
     */
    protected $profileForm;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    function getId(): string
    {
        return $this->id;
    }

    function __construct(Program $program, string $id, ProfileForm $profileForm)
    {
        $this->program = $program;
        $this->id = $id;
        $this->profileForm = $profileForm;
        $this->disabled = false;
    }

    public function disable(): void
    {
        if ($this->disabled) {
            $errorDetail = "forbidden: program's profile form already disabled";
            throw RegularException::forbidden($errorDetail);
        }
        $this->disabled = true;
    }

    public function enable(): void
    {
        if (!$this->disabled) {
            $errorDetail = "forbidden: program's profile form already enabled";
            throw RegularException::forbidden($errorDetail);
        }
        $this->disabled = false;
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->program->belongsToFirm($firm);
    }

    public function correspondWithProfileForm(ProfileForm $profileForm): bool
    {
        return $this->profileForm === $profileForm;
    }

}
