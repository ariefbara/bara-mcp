<?php

namespace Query\Domain\Task\Guest;

interface GuestTask
{

    public function execute($payload): void;
}
