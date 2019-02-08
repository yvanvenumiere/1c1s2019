<?php

namespace AlectisLab\UtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class SearchAndReplaceCommand extends ContainerAwareCommand
{

    private $arrayFiles=array
    (
        "web/styles/main_styles.css",
        "web/styles/customMain.css",
        "web/styles/responsive.css",
        "web/css/accueil.css",
        "web/styles/about.css",
        "web/styles/blog.css",
        "web/styles/blog_responsive.css",
        "web/styles/blog_single.css",
        "web/styles/blog_single_responsive.css",
        "web/styles/contact.css",
        "web/styles/contact_responsive.css",
        "web/styles/course.css",
        "web/styles/courses.css",
        "web/styles/course_responsive.css"


    );

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:sr-task')

            // the short description shown while running "php bin/console list"
            ->setDescription('Remplacement des couleurs')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("Remplacement des couleurs")

            ->addArgument('motif1', InputArgument::REQUIRED, 'Couleur 1')
            ->addArgument('motif2', InputArgument::REQUIRED, 'Couleur 2')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln([
            'Remplacement',
            '============',
            '',
        ]);


        foreach($this->arrayFiles as $file)
        {
            $output->writeln([
                'Remplacement dans le fichier '.$file,
                '============',
                '',
            ]);

            $content=file_get_contents($file);
            $newContent=str_replace($input->getArgument('motif1'),$input->getArgument('motif2'),$content);
            file_put_contents($file,$newContent);
            //echo $newContent;
        }







    }
}


