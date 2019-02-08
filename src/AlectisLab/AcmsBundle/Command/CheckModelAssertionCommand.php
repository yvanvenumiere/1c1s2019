<?php

namespace AlectisLab\AcmsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AlectisLab\UtilsBundle\Helpers\UtilClass;
use Symfony\Component\Console\Input\InputOption;


class CheckModelAssertionCommand extends ContainerAwareCommand
{
    //private $pathExporter='web/orm/exporter.php';

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('acms:cma-task')

            // the short description shown while running "php bin/console list"
            ->setDescription("Vérifie que chaque item a le même nombre d'item_values que de modelLines")
            ->addOption('idDp', "id du projet ", InputOption::VALUE_OPTIONAL, false)

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("Vérifie que chaque item a le même nombre d'item_values que de modelLines")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $idDp = $input->getOption('idDp');


        $dataBaseName=$this->getContainer()->getParameter('database_name');


        require_once "web/orm/".$dataBaseName."/libs/php/globalInfos.php";
        $ormManager=new \globalInfos(UtilClass::rewritingOrNot());

        $output->writeln([
            'Vérification',
            '============',
            '',
        ]);

        $ormManager->requireModel('item_values');
        if($idDp!=NULL)
        {
            $items=$ormManager->doQuery('item',"*","LEFT JOIN item_types ON item_types.iditem_types=item.item_types_iditem_types WHERE dataproj_iddataproj=:iddp",array(':iddp'=>$idDp));
        }
        else
        {
            $items=$ormManager->doQuery('item',"*");

        }

        foreach($items as $item)
        {
            $output->writeln([
                'Item '.$item['iditem'].'-------------'
            ]);
            //model lines disponibles
            $mls=$ormManager->doQuery('model_line',"*","WHERE model_line.item_types_iditem_types=:ittype",array(':ittype'=>$item['item_types_iditem_types']));
            $idMls=array();
            //on mets les id de model lines dans un tableau
            foreach($mls as $ml)
            {
                $idMls[]=$ml['idmodel_line'];
            }


            //item_values enregistrées
            $itemValues=$ormManager->doQuery('item_values',"*","WHERE item_values.item_iditem=:iditem",array(':iditem'=>$item['iditem']));

            //si différence dans le comptage des deux , on intervient
            if(count($itemValues)!=count($mls))
            {
                $output->writeln([
                    'Incohérence --'
                ]);



                $mlExist=array();
                foreach($itemValues as $iv)
                {
                    $mlExist[]=$iv['model_line_idmodel_line'];
                }


                //CAS DES MODELLINES EXISTANTS SANS ITEMVALUES CORRESPONDANTS
                /////////////////////////////////////////////////////////////
                //on répertorie les iv existants et leur modelline

                //on parcourt les modelLines officiels
                foreach($mls as $ml)
                {

                    if(!in_array($ml['idmodel_line'],$mlExist))//si les iv existants n'ont pas certains modelline, on crée
                    {
                        $objIv=new \item_values(UtilClass::rewritingOrNot());
                        $objIv->set('item_iditem',$item['iditem']);
                        $objIv->set('model_line_idmodel_line',$ml['idmodel_line']);
                        $objIv->save();
                        $output->writeln([
                            'Un item value sauvegardé'
                        ]);


                    }
                }


                //CAS DES ITEMVALUES EXISTANTS SANS MODELLINES CORRESPONDANTS
                /////////////////////////////////////////////////////////////

                //on parcourt les
                foreach($mlExist as $idMl)
                {
                    if(!in_array($idMl,$idMls))
                    {

                    }
                }
            }
            $output->writeln([
                '----------------------------------------- ',
                '----------------------------------------- '
            ]);

        }




    }
}


