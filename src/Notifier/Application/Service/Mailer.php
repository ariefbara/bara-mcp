<?php

namespace Notifier\Application\Service;

interface Mailer
{
    public function send(CanBeMailedInterface $mail): void;
}
