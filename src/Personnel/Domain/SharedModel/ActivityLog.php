<?php

namespace Personnel\Domain\SharedModel;

use DateTimeImmutable;
use Personnel\Domain\{
    Model\Firm\Personnel\ProgramConsultant,
    SharedModel\ActivityLog\ConsultantActivityLog
};
use Resources\DateTimeImmutableBuilder;

class ActivityLog
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
    protected $message;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $occuredTime;

    /**
     *
     * @var ConsultantActivityLog
     */
    protected $consultantActivityLog;

    public function __construct(string $id, string $message, ProgramConsultant $consultant)
    {
        $this->id = $id;
        $this->message = $message;
        $this->occuredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->consultantActivityLog = new ConsultantActivityLog($this, $id, $consultant);
    }

}
