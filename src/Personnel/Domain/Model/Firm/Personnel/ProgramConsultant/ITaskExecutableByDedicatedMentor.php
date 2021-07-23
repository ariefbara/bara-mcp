<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

interface ITaskExecutableByDedicatedMentor
{
    public function execute(DedicatedMentor $dedicatedMentor): void;
}
