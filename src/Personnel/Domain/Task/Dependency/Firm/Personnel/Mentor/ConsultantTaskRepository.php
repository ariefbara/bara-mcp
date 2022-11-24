<?php

namespace Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantTask;

interface ConsultantTaskRepository
{

    public function nextIdentity(): string;

    public function add(ConsultantTask $consultantTask): void;

    public function ofId(string $id): ConsultantTask;
}
