<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\AssetBelongsToFirm;
use Firm\Domain\Model\Firm;
use Resources\Exception\RegularException;

class ClientCVForm implements AssetBelongsToFirm
{

    /**
     * 
     * @var Firm
     */
    protected $firm;

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

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Firm $firm, string $id, ProfileForm $profileForm)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->profileForm = $profileForm;
        $this->disabled = false;
    }

    public function disable(): void
    {
        if ($this->disabled) {
            $errorDetail = "forbidden: client cv form already disable";
            throw RegularException::forbidden($errorDetail);
        }
        $this->disabled = true;
    }

    public function enable(): void
    {
        if (!$this->disabled) {
            $errorDetail = "forbidden: client cv form already enabled";
            throw RegularException::forbidden($errorDetail);
        }
        $this->disabled = false;
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

    public function correspondWithProfileForm(ProfileForm $profileForm): bool
    {
        return $this->profileForm === $profileForm;
    }

}
