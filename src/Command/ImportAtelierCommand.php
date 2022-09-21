<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:atelier',
    description: 'Add a short description for your command',
)]
class ImportAtelierCommand extends Command
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager, string $name = null)
    {
        $this->manager = $manager;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $ateliers = json_decode(file_get_contents('./Data/ateliers.json'), true);

        $str = "";

        foreach ($ateliers['data'] as $atelier){
//            $io->text("insert into atelier (name) values (\"$atelier\")");
            $temp = 'insert into atelier (name) values (\"'.$atelier.'\")';
            $str .= '$this->addSql("'.$temp.'")'."\n";
        }

        file_put_contents('zaki.txt', $str);

        $io->section('begin import...');

        $io->success('Import Successfully');

        return Command::SUCCESS;
    }
}
