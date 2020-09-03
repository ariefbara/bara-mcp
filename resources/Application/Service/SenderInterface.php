<?php

namespace Resources\Application\Service;

interface SenderInterface
{

    public function getMailAddress(): string;

    public function getName(): string;
}
