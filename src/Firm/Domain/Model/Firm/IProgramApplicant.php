<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;

interface IProgramApplicant
{

    public function assertBelongsInFirm(Firm $firm): void;

    public function getUserType(): string;
}
