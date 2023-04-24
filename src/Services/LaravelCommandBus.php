<?php

namespace Chuajose\CommandBus\Services;

use Illuminate\Bus\Dispatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class LaravelCommandBus implements CommandBusInterface
{
    use Queueable;
    public function __construct(private readonly Dispatcher $bus) {}

    public function dispatch($command): void
    {

        if($this->bus->hasCommandHandler($command)){
            $this->bus->dispatch($command);
        }else{
            Log::emergency("No se ha encontrado el handler para el comando: ".get_class($command));
        }

    }

    public function map(array $map): void
    {
        $this->bus->map($map);
    }
}
