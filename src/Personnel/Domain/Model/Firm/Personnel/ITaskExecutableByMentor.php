<?php

namespace Personnel\Domain\Model\Firm\Personnel;

interface ITaskExecutableByMentor
{
    public function execute(ProgramConsultant $mentor): void;
}
