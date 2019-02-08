<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
//use AppBundle\Helpers\UtilClass;

class HandleBddCommand extends ContainerAwareCommand
{
    private $pathExporter='web/orm/exporter.php';

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:handlebdd-task')

            // the short description shown while running "php bin/console list"
            ->setDescription('Génération du model')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("Génération du model")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln([
            'Génération du model',
            '============',
            '',
        ]);

        $dataBaseName=$this->getContainer()->getParameter('database_name');
        $dataBaseUser=$this->getContainer()->getParameter('database_user');
        $dataBaseMdp=$this->getContainer()->getParameter('database_password');
        $dataBaseHost=$this->getContainer()->getParameter('database_host');

        //echo $dataBaseName;exit;

        system("cd web/orm");
        system("cd web/orm \n php exporter.php ".$dataBaseName." ".$dataBaseUser." ".$dataBaseMdp." ".$dataBaseHost);





    }
}


