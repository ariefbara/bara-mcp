<?php

namespace Resources\Application\Service;

interface RecipientInterface
{

    public function getMailAddress(): string;

    public function getName(): string;
}
