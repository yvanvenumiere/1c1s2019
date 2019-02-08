<?php

namespace AlectisLab\AcmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AlectisLab\UtilsBundle\Helpers\UtilClass;
use Symfony\Component\Console\Input\InputOption;


class CleanMediasCommand extends ContainerAwareCommand
{
    //private $pathExporter='web/orm/exporter.php';

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('acms:clean-medias-task')

            // the short description shown while running "php bin/console list"
            ->setDescription('Suppression des images orphelines')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("Suppression des images orphelines")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {






        $dataBaseName=$this->getContainer()->getParameter('database_name');


        require_once "web/orm/".$dataBaseName."/libs/php/globalInfos.php";
        $ormManager=new \globalInfos(UtilClass::rewritingOrNot());

        $output->writeln([
            'Suppression des images orphelines',
            '============',
            '',
        ]);

        $ormManager->requireModel('medias');
        $medias=$ormManager->doQuery('medias',"*","LEFT JOIN item_value_media ON item_value_media.medias_idmedias=medias.idmedias LEFT JOIN fixed_data_media ON fixed_data_media.medias_idmedias=medias.idmedias WHERE medias.media_date_creation<:expire",array(':expire'=>time()-3600));
        foreach($medias as $med)
        {
            if($med['iditem_value_media']==null && $med['idfixed_data_media']==null)
            {

                $mediaToDelete=new \medias(UtilClass::rewritingOrNot());

                if($mediaToDelete->initFromDatas(array('idmedias'=>$med['idmedias'])))
                {
                    echo "delete ".$med['idmedias'];
                    @unlink("web/".$mediaToDelete->get('media_path'));
                    $mediaToDelete->delete();
                }
            }
        }




    }
}


