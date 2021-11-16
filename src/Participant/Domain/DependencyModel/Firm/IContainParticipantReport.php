<?php

namespace Participant\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

interface IContainParticipantReport
{

    public function processReport(Form $form, FormRecordData $formRecordData, int $mentorRating): void;
}
