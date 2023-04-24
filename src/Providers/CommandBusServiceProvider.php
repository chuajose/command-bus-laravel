<?php

namespace Chuajose\CommandBus\Providers;


use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionMethod;
use Upthemedia\CommandBus\Services\CommandBusInterface;
use Upthemedia\CommandBus\Services\CommandHandlerInterface;
use Upthemedia\CommandBus\Services\CommandInterface;
use Upthemedia\CommandBus\Services\LaravelCommandBus;

class CommandBusServiceProvider extends ServiceProvider
{
    protected array $commands = [];

    public function boot(): void
    {
        $this->app->singleton(CommandBusInterface::class, LaravelCommandBus::class);

        $this->loadCommands();

    }

    /**
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    private function loadCommands(): void
    {
        $bus = $this->app->make(CommandBusInterface::class);
        $listeners = [];
        foreach (glob(app_path('Domains/*/Commands/*.php')) as $file) {

            $class = str_replace($this->app->path, '',$file);
            $class = ucfirst(basename(app()->path())).''.$class;
            $className = (str_replace(
                [DIRECTORY_SEPARATOR],
                ['\\'],
                ucfirst(Str::replaceLast('.php', '', $class))
            ));
            $command = new \ReflectionClass($className);
            $commandInterface = new \ReflectionClass(CommandInterface::class);

            if(in_array($commandInterface ,$command->getInterfaces())){
                $this->loadHandlersFromCommand($command);
            }
        }

        $bus->map($this->commands);
    }

    /**
     * @throws \ReflectionException
     */
    private function loadHandlersFromCommand(\ReflectionClass $command): void
    {
        foreach (glob(app_path('Domains/*/Commands/*.php')) as $file) {
            $class = str_replace($this->app->path, '',$file);
            $class = ucfirst(basename(app()->path())).''.$class;
            $className = (str_replace(
                [DIRECTORY_SEPARATOR],
                ['\\'],
                ucfirst(Str::replaceLast('.php', '', $class))
            ));
            $handler = new \ReflectionClass($className);
            $commandHandlerInterface = new \ReflectionClass(CommandHandlerInterface::class);
            //Si el handler implementa la interfaz CommandHandlerInterface
            if(in_array($commandHandlerInterface ,$handler->getInterfaces())){
                foreach ($handler->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {


                    if ((! Str::is('handle', $method->name) && ! Str::is('__invoke', $method->name)) ||
                        ! isset($method->getParameters()[0])) {

                        continue;
                    }

                    if($method->getParameters()[0]->getType()->getName() === $command->name){
                        $this->commands[$command->name] = $handler->name;

                    }

                }
            }

        }


    }
}
