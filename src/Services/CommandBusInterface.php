<?php

namespace Upthemedia\CommandBus\Services;

interface CommandBusInterface
{
    public function dispatch($command): void;
    public function map(array $map): void;
}
