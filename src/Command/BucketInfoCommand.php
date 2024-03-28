<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bucket:info',
    description: 'information about the comand in this project',
)]
class BucketInfoCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);



        $io->success('
______            _        _          _     _     _   
| ___ \          | |      | |        | |   (_)   | |  
| |_/ /_   _  ___| | _____| |_ ______| |    _ ___| |_ 
| ___ \ | | |/ __| |/ / _ \ __|______| |   | / __| __|
| |_/ / |_| | (__|   <  __/ |_       | |___| \__ \ |_ 
\____/ \__,_|\___|_|\_\___|\__|      \_____/_|___/\__|
                                                      
                                                      ');
        $io->write('Liste de toutes les commandes disponible !');

        return Command::SUCCESS;
    }
}
