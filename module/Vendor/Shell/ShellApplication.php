<?php


namespace Module\Vendor\Shell;


use Illuminate\Console\Application;
use Illuminate\Events\Dispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShellApplication extends Application
{
    /**
     * Create a new console application.
     *
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'Console Application', $version = '1.0.0')
    {
        parent::__construct($laravel = new \Illuminate\Foundation\Application(), new Dispatcher($laravel), $version);

        $this->setName($name);
        $this->setAutoExit(true);
        $this->setCatchExceptions(true);
    }

    /**
     * Register a Closure based command.
     *
     * @param string $signature
     * @param \Closure $callback
     * @param string $description
     * @return Command
     */
    public function command($signature, \Closure $callback, $description = null)
    {
        return $this->add(
            (new ClosureCommand($signature, $callback))->describe($description)
        );
    }

    /**
     * Run the current application as a single command application.
     *
     * @param \Symfony\Component\Console\Input\InputInterface|null $input
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     * @return int
     */
    public function runAsSingle(InputInterface $input = null, OutputInterface $output = null)
    {
        foreach ($this->all() as $command) {
            $this->setDefaultCommand($command, true);
            break;
        }

        return $this->run($input, $output);
    }
}
