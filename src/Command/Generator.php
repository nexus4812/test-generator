<?php

namespace Nexus4812\TestGenerator\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'create-unit-test')]
class Generator extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        return Command::SUCCESS;
    }
}
