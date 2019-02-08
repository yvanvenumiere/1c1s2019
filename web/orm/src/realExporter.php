<?php
ini_set("display_errors", "1");
//function handles export the generation of the backoffice
function export($base,$generationSimple,$infos,$user,$mdp,$dbh)
{
    try {
        //we get the tables
        $resultFindTables=getTables($dbh, $base);
        //echo "<pre>";var_dump($resultFindTables);exit;
        $arrayConstructHierarchy=array();



        //we start the xml menu
        $xmlMenu = new XMLWriter();
        $xmlMenu -> openUri($base . "/config/menu.xml");
        $xmlMenu -> setIndent(true);
        $xmlMenu -> setIndentString("     ");
        $xmlMenu -> startDocument("1.0","UTF-8");
        $xmlMenu -> startElement("menu");

        foreach ($resultFindTables as $table) {

            //we empty the xml menu
            $xmlMenu -> startElement("aMenu");
            $xmlMenu -> writeAttribute("tableName", $table["Tables_in_" . $base . ""]);
            $xmlMenu -> writeAttribute("displayName", $table["Tables_in_" . $base . ""]);
            $xmlMenu -> endElement();

            //we initialize a var that contains the infos on the table
            $contenu;
            //we empty the variable
            $contenu = getContenu("simple", $table["Tables_in_" . $base . ""]);

            //we search the different relations on the table
            $sqlFindRelations = "SELECT 
	        ke.column_name col, 
	        ke.referenced_table_schema assoc_db,
	        ke.referenced_table_name assoc_table,
	        ke.referenced_column_name assoc_col
		    FROM
	        information_schema.KEY_COLUMN_USAGE ke
		    WHERE
	        ke.referenced_table_name IS NOT NULL              
		    AND   ke.table_schema='" . $base . "'
		    AND   ke.table_name='" . $table["Tables_in_" . $base . ""] . "'";

            //we searh the different fields on this table
            $sqlReqFindFields = "DESCRIBE " . $table["Tables_in_" . $base . ""] . "";

            $prepaFindFields = $dbh -> query($sqlReqFindFields);

            $prepaFindFields -> setFetchMode(PDO::FETCH_ASSOC);
            $resultFindFields = $prepaFindFields -> fetchAll();


            //we start a variable that will contain the generated code for the declaration of the differents fields in the future class
            $arrayRoutineFields = "";

            //we start the xml configuration of the table we are into in the loop
            $xml = new XMLWriter();
            $xml -> openUri($base . "/config/" . $table["Tables_in_" . $base . ""] . ".xml");
            $xml -> setIndent(true);
            $xml -> setIndentString("     ");
            $xml -> startDocument("1.0", "UTF-8");
            $xml -> startElement("formDescription");
            $xml -> writeAttribute('reqLimit', 10);
            $xml -> writeAttribute('formOrder', "");
            //and for each field ...
            foreach ($resultFindFields as $key => $field) {
                //we start a xml field
                $xml -> startElement($field['Field']);
                $xml -> writeAttribute('isGridVisible', 'true');//...
                $xml -> writeAttribute('dataType', $field['Type']);//...
                //if its not a primary key , we put it in the form, we do a special treatment for the primary keys
                if ($field['Key'] != "PRI")
                {
                    $xml -> writeAttribute('isVisibleInForm', 'true');//...
                    $xml -> writeAttribute('isFilterVisible', 'true');//...
                }
                else //if it's a primary key)
                {
                    if ($key == 0) {//if it(s the first, it(s the one that we define has the primary key...))
                        $xml -> writeAttribute('isVisibleInForm', 'false');//...
                        $xml -> writeAttribute('isPrimaryKey', 'true');//...
                        $xml -> writeAttribute('isFilterVisible', 'false');//...

                        /*$xml -> startElement('pushHierarchy');//...
                            $xml->writeAttribute('className',' a class name');
                            $xml->writeAttribute('promptLabel',' prompt label');
                            $xml->writeAttribute('enabled','false');
                            $xml->writeAttribute('listIcone',' url of an icon');
                            $xml->writeAttribute('displayColumn','');
                            $xml->writeAttribute('displayDenomination','');
                        $xml->endElement();

                        $xml -> startElement('getHierarchy');//...
                            $xml->writeAttribute('className',' a class name');
                            $xml->writeAttribute('promptLabel',' prompt label');
                            $xml->writeAttribute('enabled','false');
                        $xml->endElement();*/

                    } else {//else maybe it's a foreign key so we put a display column label ...
                        $xml -> writeAttribute('isVisibleInForm', 'true');//...
                        $xml -> writeAttribute('isFilterVisible', 'true');//...
                        $xml -> writeComment("in case of forein key , we can specify the column we choose to display datas ..??");//comment
                        $xml->startElement('displayColumn');
                        $xml->writeAttribute('sortColumn','');
                        $xml->text('');
                        $xml->endElement();
                    }

                }

                //label diplayed in the forms
                $xml -> writeComment('label displayed in the form');
                $xml -> writeElement("label", $field['Field']);

                //maybe it's a foreign key so we put a display column label ...
                if ($field['Key'] == "MUL") {
                    $xml -> writeComment("in case of forein key , we can specify the column we choose to display datas ..??");
                    $xml->startElement('displayColumn');
                    $xml->writeAttribute('sortColumn','');
                    $xml->text('');
                    $xml->endElement();
                }

                //type of checking
                $xml -> writeComment('type of checking for the field : simple,mail,optional,int');
                $xml -> writeElement("cheking", "");

                //*******************************************picture upload**************************************************************//

                //infos
                $xml -> writeComment('if you want to upload picture , you have to fill the infos in this node , if the enabled attribute is set to true, the configuration will work');
                $xml -> startElement("pictureField");
                $xml -> writeAttribute("enabled", "false");
                $xml -> writeAttribute("enabledForRelation", "false");

                $xml -> writeComment('each child node represents a saving of the picture, resizeType can be set to resizeFix, resizeHomo, resizeHomoW, resizeHomoH, noResize; the path attribute is the path of the uploaded pictures, it must be relative to the index.php file of the admin directory wich is created and need end slash');
                $xml -> startElement("picture");
                $xml -> writeAttribute("resizeType", "type of resizing");
                $xml -> writeAttribute("width", "width in pixels");
                $xml -> writeAttribute("height", "height in pixels");
                $xml -> writeAttribute("ratio", "height in pixels");
                $xml -> writeAttribute("path", "path for the picture");
                $xml -> writeAttribute("isForAdmin", "false");

                //accepted formats
                $xml -> startElement("acceptedFormats");
                $xml -> writeComment('each child node represents a format of picture');
                $xml -> writeElement("format", ".jpg");
                $xml -> endElement();
                $xml -> endElement();
                $xml -> endElement();
                //*********************************************************************************************************//

                //*******************************************file upload**************************************************************//


                //infos
                $xml -> writeComment('if you want to upload files , you have to fill the infos in this node , if the enabled attribute is set to true, the configuration will work');

                $xml -> startElement("fileField");
                $xml -> writeAttribute("enabled", "false");
                $xml -> writeComment('configuration for the upload');
                $xml -> startElement("aFile");
                //$xml -> writeComment('the path attribute is the path of the uploaded file, it must be relative to the index.php file of the admin directory wich is created and need end slash');
                //$xml -> writeAttribute("path", "path for the file");
                $xml -> writeAttribute("path", "path for the file");

                $xml -> writeComment('each child node represents an accepted format');

                //accepted formats
                $xml -> startElement("acceptedFormats");
                $xml -> writeElement('format', '.extension');
                $xml -> endElement();

                $xml -> endElement();
                $xml -> endElement();
                //*********************************************************************************************************//


                //date fields
                $xml -> writeComment('the informations can be a date... in this case we save a timestamp in the bdd');
                $xml -> startElement("dateField");
                $xml -> writeAttribute("enabled", "false");
                $xml -> endElement();

                //boolean fields
                $xml -> writeComment('the informations can be a boolean... in this case we save a tinyint in the bdd');
                $xml -> startElement("booleanField");
                $xml -> writeAttribute("enabled", "false");
                $xml->writeElement("boolean_true_label","oui");
                $xml->writeElement("boolean_false_label","non");
                $xml -> endElement();

                //riche editor field
                $xml -> writeComment('the informations can be text, so we can implement a rich text editor, format can be dd/mm/yyyy or mm/dd/yyyy ');
                $xml -> startElement("richTextEditorField");
                $xml -> writeAttribute("enabled", "false");
                $xml -> endElement();

                //riche editor field
                $xml -> writeComment('if you want to make a colorpicker field');
                $xml -> startElement("colorPickerField");
                $xml -> writeAttribute("enabled", "false");
                $xml -> endElement();

                //we empty this variable that contains the generated code for the declaration of the differents fields in the future class
                $arrayRoutineFields .= '
				$this->arrayFields["' . $field['Field'] . '"]=array
				(
					"type"=>"' . $field['Type'] . '",
					"canBeNull"=>"' . $field['Null'] . '",
					"key"=>"' . $field['Key'] . '",
					"default"=>"' . $field['Default'] . '",
					"extra"=>"' . $field['Extra'] . '",
					"formLabel"=>"' . $field['Field'] . '"
				);
			';
                $xml -> endElement();
            }
            $xml -> writeComment('this node contains all the callbacks for the differents action on this model ');
            $xml->startElement("callbacks");
            $xml->writeComment('action that is executed when an element is saved');
            $xml->startElement('onSave');
            $xml->writeAttribute('fileName','');
            $xml->writeAttribute('className','');
            $xml->writeAttribute('methodName','');
            $xml->endElement();

            $xml->startElement('onDelete');
            $xml->writeAttribute('fileName','');
            $xml->writeAttribute('className','');
            $xml->writeAttribute('methodName','');
            $xml->endElement();

            $xml->startElement('onUpdate');
            $xml->writeAttribute('fileName','');
            $xml->writeAttribute('className','');
            $xml->writeAttribute('methodName','');
            $xml->endElement();


            $xml->endElement();
            $xml -> endElement();
            $xml -> endDocument();
            $xml -> flush();

            // done for the xml of the model and the cruds

            //this query is used to find all the relation for the table in the loop
            $prepaFindRelations = $dbh -> query($sqlFindRelations);
            $prepaFindRelations -> setFetchMode(PDO::FETCH_ASSOC);
            $resultFindRelations = $prepaFindRelations -> fetchAll();
            $arrayConstructHierarchy[$table["Tables_in_" . $base . ""]]=$resultFindRelations;
            $arrayRelations = "";
            //$xmlHierarchy->startElement($table["Tables_in_" . $base . ""]);
            foreach ($resultFindRelations as $relation) {
                $arrayRelations .= '
				$this->arrayRelations["' . $relation["col"] . '"]=array
				(
					"assoc_table"=>"' . $relation["assoc_table"] . '",
					"assoc_col"=>"' . $relation["assoc_col"] . '"
				);
			';

            }


            //$xmlHierarchy->endElement();



            //we empty some variables
            $newContent = str_replace("{arrayFieldRoutine}", $arrayRoutineFields, $contenu);
            $newContent = str_replace("{arrayRelations}", $arrayRelations, $newContent);

            //we empty some files ...
            file_put_contents($base . "/model/" . $table["Tables_in_" . $base . ""] . ".php", $newContent);//models
            file_put_contents($base . "/libs/php/inclusion_" . $table["Tables_in_" . $base . ""] . ".php", '<?php $datasManager=new ' . $table["Tables_in_" . $base . ""] . 'DatasManager(); $oneElement=new ' . $table["Tables_in_" . $base . ""] . '(); ?>'); // inclusion files
            file_put_contents($base . "/libs/php/inclusion_bididi.php", '<?php
				$infos="' . $infos . '";
				$user="' . $user . '";
				$mdp="' . $mdp . '";
				$dbh=new PDO($infos,$user,$mdp,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		     ?>');//database definition
        }

        $xmlMenu -> endElement();
        $xmlMenu -> endDocument();
        $xmlMenu -> flush();//	done for the xml of the backend menu




        //we create a xml for the global labels, formats etc
        $xmlGlobalConfig = new XMLWriter();
        $xmlGlobalConfig -> openUri($base . "/config/global_config.xml");
        $xmlGlobalConfig -> setIndent(true);
        $xmlGlobalConfig -> setIndentString("     ");
        $xmlGlobalConfig -> startDocument("1.0", "UTF-8");
        $xmlGlobalConfig -> startElement("global_config");
        $xmlGlobalConfig ->startElement("generic");
        $xmlGlobalConfig -> writeElement("global_date_format","dd-mm-yy");
        $xmlGlobalConfig -> writeElement("global_add_label","Ajouter un élément");
        $xmlGlobalConfig -> writeElement("global_filter_label","Filtres");
        $xmlGlobalConfig -> writeElement("global_search_label","Rechercher");
        $xmlGlobalConfig -> writeElement("global_delete_confirm_label","Etes vous sur de vouloir effacer cet élément ?");
        $xmlGlobalConfig -> writeElement("global_delete_label","effacer");
        $xmlGlobalConfig -> writeElement("global_edit_label","éditer");
        $xmlGlobalConfig -> writeElement("global_edit_title_label","Modifier l'élement");
        $xmlGlobalConfig -> writeElement("global_add_title_label","Informations sur l'élement");
        $xmlGlobalConfig -> writeElement("global_save_label","Sauvegarder l'élément");
        $xmlGlobalConfig -> writeElement("global_cancel_label","Annuler");
        $xmlGlobalConfig -> writeElement("global_error_image_label","L'image n'a pu être récupéré");
        $xmlGlobalConfig -> writeElement("global_error_file_label","Le fichier n'a pu être récupéré");
        $xmlGlobalConfig -> writeElement("global_error_fields_label","Vérifiez d'avoir bien rempli les champs surlignés en rouge");
        $xmlGlobalConfig -> writeElement("global_login","login");
        $xmlGlobalConfig -> writeElement("global_password","password");
        $xmlGlobalConfig -> writeElement("global_login_label","Identifiant");
        $xmlGlobalConfig -> writeElement("global_password_label","Mot de passe");
        $xmlGlobalConfig -> writeElement("global_wrong_login","login incorrect");
        $xmlGlobalConfig -> writeElement("global_disconnect","Se déconnecter");
        $xmlGlobalConfig -> writeElement("global_connect","Se connecter");
        $xmlGlobalConfig ->endElement();

        $xmlGlobalConfig -> endElement();
        $xmlGlobalConfig -> endDocument();
        $xmlGlobalConfig -> flush();//	done


        //we start the hierarchy xml
        $xmlHierarchy = new XMLWriter();
        $xmlHierarchy -> openUri($base . "/config/hierarchy.xml");
        $xmlHierarchy -> setIndent(true);
        $xmlHierarchy -> setIndentString("     ");
        $xmlHierarchy -> startDocument("1.0","UTF-8");
        $xmlHierarchy -> startElement("hierarchy");
        $arrayReverseHierarchy=array();
        foreach($arrayConstructHierarchy as $key=>$value)
        {
            $xmlHierarchy->startElement($key);
            $xmlHierarchy->writeAttribute('refName',$key);
            $xmlHierarchy->writeAttribute('displayColumn',"");
            $xmlHierarchy->startElement("parentTables");
            foreach($value as $value2)
            {
                $xmlHierarchy->startElement("parentTable");
                if(!isset($arrayReverseHierarchy[$value2['assoc_table']]))
                {
                    $arrayReverseHierarchy[$value2['assoc_table']]=array();
                }
                $arrayReverseHierarchy[$value2['assoc_table']][]=array("className"=>$key,"usedCol"=>$value2['assoc_col'],"fk"=>$value2['col']);
                $xmlHierarchy->writeAttribute("className",$value2['assoc_table']);
                $xmlHierarchy->writeAttribute("fk",$value2['col']);
                $xmlHierarchy->writeAttribute("usedCol",$value2['assoc_col']);
                $xmlHierarchy->writeAttribute("enabled","false");
                $xmlHierarchy->writeAttribute("prompt","");
                $xmlHierarchy->writeAttribute("icon","");
                //$xmlHierarchy->writeAttribute("refName",$value2['assoc_table']);
                /*foreach($value2 as $key3=>$value3)
                {
                    $xmlHierarchy->writeAttribute($key3,$value3);

                }*/
                $xmlHierarchy->endElement();
            }
            $xmlHierarchy->endElement();
            $xmlHierarchy->endElement();

        }



        $xmlHierarchy->endElement();
        $xmlHierarchy -> endDocument();
        $xmlHierarchy -> flush();

        $xmlAgain=simplexml_load_file($base . "/config/hierarchy.xml");

        //echo "<pre>";var_dump($arrayReverseHierarchy);

        foreach($arrayReverseHierarchy as $key=>$value)
        {
            $xmlAgain->{"".$key.""}->addChild("childTables","");
            foreach($value as $key2=>$value2)
            {
                $xmlAgain->{"".$key.""}->childTables->addChild("childTable");
                foreach($value2 as $key3=>$value3)
                {
                    $xmlAgain->{"".$key.""}->childTables->childTable[$key2]->addAttribute($key3,$value3);
                }
                $xmlAgain->{"".$key.""}->childTables->childTable[$key2]->addAttribute("enabled","false");
                $xmlAgain->{"".$key.""}->childTables->childTable[$key2]->addAttribute("prompt","");
                $xmlAgain->{"".$key.""}->childTables->childTable[$key2]->addAttribute("icon","");
                //$xmlAgain->{"".$key.""}->childTables->childTable[$key2]->addAttribute("refName",$key);

            }
        }
        $xmlAgain->asXml($base . "/config/hierarchy.xml");
    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
    }

}

function getTables($dbh,$base)
{
	//we find all the tables of the database
	$sqlFindTables = "SHOW TABLES FROM " . $base . "";

	$prepaFindTables = $dbh -> query($sqlFindTables);
	$prepaFindTables -> setFetchMode(PDO::FETCH_ASSOC);
	$resultFindTables = $prepaFindTables -> fetchAll();
	return $resultFindTables;
}


?>