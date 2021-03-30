<?php namespace ffta_extractor;

// classement TAE International ("TI") ou National ("TN"), "T" si ind�fini ou erreur NULL
function type_TAE($discipline,$cat,$arme,$distance,$blason)
{   $TAE=NULL;
    if ($discipline == "T"){
        $TAE="T";
    } else {
        $TAE=NULL;
        return $TAE;
    }

    if( $arme == "CL" ) {
        switch ($cat) {
            case "S3":
            case "C":
                if( $distance == "60" && $blason == "122") {
                    $TAE = "TI";
                }
                elseif ( $distance == "50" && $blason == "122") {
                    $TAE = "TN";
                }
                break;

            case "S2":
            case "S1":
            case "J":
                if( $distance == "70" && $blason == "122") {
                    $TAE = "TI";
                }
                elseif ( $distance == "50" && $blason == "122") {
                    $TAE = "TN";
                }
                break;


            case "M":
                if( $distance == "40" && $blason == "80") {
                    $TAE = "TI";
                } elseif ( $distance == "30" && $blason == "80") {
                    $TAE = "TN";
                }
                break;

            case "B":
                if( $distance == "30" && $blason == "80") {
                    $TAE = "TI";
                } elseif ( $distance == "20" && $blason == "80") {
                    $TAE = "TN";
                }
                break;

            case "P":
                if( $distance == "20" && $blason == "80") {
                    $TAE = "TI";
                }
                break;
            //default:
                //echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
        }

    }
    elseif ($arme == "CO")
    {
        switch ($cat)
        {
            case "S3":
            case "S2":
            case "S1":
            case "J":
            case "C":
                if( $distance == "50" && $blason == "80")
                {
                    $TAE = "TI";
                } elseif( $distance == "50" && $blason == "122")
                {
                    $TAE = "TN";
                }
                break;
            //default:
                //echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
        }
    }
    /*
     else
     {
     echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
     }
     */
    return $TAE;
}


class Importer
{
    private $_file_storage_path;

    private $_configuration;

    public function __construct( $configuration )
    {
        $this->_configuration = $configuration;

        $this->_file_storage_path = dirname(__FILE__).'/../data/imported';
        if(! is_dir ( $this->_file_storage_path ) ){
            mkdir($this->_file_storage_path, 0750);
        }
    }


    public function import_file( $request_id ){
        if(isset($_FILES[$request_id])){
            $file_name = $_FILES[$request_id]['name'];
            $file_size =$_FILES[$request_id]['size'];
            $file_tmp =$_FILES[$request_id]['tmp_name'];
            $file_type=$_FILES[$request_id]['type'];
            $file_path_trim = explode('.',$file_name);
            $file_path_trim_end = end($file_path_trim);
            $file_ext=strtolower($file_path_trim_end);

            if($file_ext != "txt"){
                echo "extension not allowed, please choose a txt file.";
                die();
            }
            if($file_size > 2097152){
                echo "File size must be less than 2 MB";
                die();
            }

            // indique la date d'importation pour eviter d'ecraser les fichiers du meme club
            $str_date = date("Y-m-d_His_");
            $file_path = $this->_file_storage_path."/".$str_date.$file_name;

            // check file contains VERSION on line 1, 2021-03-28
            $file_contant = file($file_tmp);
            foreach($file_contant as $line)
            {

                $data = str_getcsv(utf8_encode($line), "\t");
                $str_version = $data[0];
                if (!stristr($str_version, 'VERSION'))
                {
                    echo "Fichier invalide " . $file_path
                    ."</br> Verifiez le fichier TXT ResultArc, puis contactez le webmaster web@arc-occitanie.fr</br>";
                    die();
                }
                break;
            }


            if(! file_exists ( $file_path ) ){
                echo "WARNING : File".$file_name." already uploaded today</br>";
            }

            move_uploaded_file($file_tmp,$file_path);

            return $file_path;
        }
    }

    public function analyse_file( $file_path ){

        if(! file_exists ( $file_path ) ){
            echo "File".$file_path." does not exist";
            die();
        }

        $scores = array();

        $file_contant = file($file_path);
        $cpt_line = 0;
        $version=0;
        $nb_scores=0;
        foreach($file_contant as $line)
        {
            $data = str_getcsv(utf8_encode($line), "\t");
            ++$cpt_line;
            if ($cpt_line == 1) // premiere ligne: "VERSION: 7.11"
            {
                $str_version = $data[0];
                //if (!str_contain($str_version, 'VERSION')) // PHP 8 only!
                if (!stristr($str_version, 'VERSION'))
                {
                    echo "Fichier invalide " . $file_path
                    ."</br> Verifiez le fichier TXT ResultArc, puis contactez le webmaster web@arc-occitanie.fr</br>";
                }
                $version = $data[1];
                echo $str_version . $version ."</br>";
                continue;
            }
            if ($version < 1) continue;
            if ($version < 7)
            {
                if ($cpt_line <= 2) continue; // 2 lignes version <7
            }
            elseif ($cpt_line <= 4) continue; // 4 lignes version 7.11
                
                
            {   // Calcul des elements sans mapping direct avec le fichier resultarc :

                $discipline = $data[0];
                // Discipline :
                // S = 'Salle',
                // T = TAE (I et N)
                // C = Campagne
                // 3 = 3D
                // N = Nature
                // B = Bersault
                $nom = $data[4];
                $prenom = $data[5];
                $cat_a = $data[6]; // classe d'age
                $cat = $data[7]; // surclassement
                if (strlen($cat) <1) $cat = $cat_a;
                $arme = $data[9];
                $club_archer= $data[11];
                // Region, departement
                $code_structure = $data[12];
                $region = substr($code_structure, 0, 2);
                $departement = substr($code_structure, 2, 2);

                $lieu_concours= $data[20];
                $distance = $data[17];
                $blason = $data[18];

                $date_score = $data[19]; // d�but concours
                // Saison
                $date = \DateTime::createFromFormat("d/m/Y", $date_score);
                $annee = intval($date->format("Y"));
                $mois = intval($date->format("m"));
                $jour = intval($date->format("d"));

                $saison = strval( $annee );
                if( $mois > 8 ){
                    $saison = strval( $annee + 1 );
                }

                // Dans le cas d'un TAE, il faut determiner si c'est un TAE I ou TAE N :
                if( $discipline == "T" )
                {
                    $TAE= type_TAE($discipline,$cat,$arme,$distance,$blason);
                    if (isset($TAE))
                        $discipline = $TAE;
                    else
                        echo "Erreur : Discipline TAE impossible ".$nom." ".$prenom ."</br>";
                }

                $score = array(
                    "nom" => $nom, 
                    "prenom" => $prenom, 
                    "no_licence" => $data[3], 
                    "saison" => $saison, 
                    "date_score" => $date_score, 
                    "sexe" => $data[8], 
                    "cat" => $cat, 
                    "code_structure" => $code_structure, 
                    "region" => $region, 
                    "departement" => $departement, 
                    "discipline" => $discipline, 
                    "arme" => $data[9], 
                    "score" => $data[13], 
                    "lieu_concours" => $lieu_concours,
                    "distance" => $distance, 
                    "blason" => $blason, 
                    "num_depart" => $data[50] );
                $scores[] = $score;
                echo "Inserion de : ";
                echo "nom: ".$score["nom"].", ";
                echo "prenom: ".$score["prenom"].", ";
                echo "no_licence: ".$score["no_licence"].", ";
                echo "saison: ".$score["saison"].", ";
                echo "date_score: ".$score["date_score"].", ";
                echo "sexe: ".$score["sexe"].", ";
                echo "cat: ".$score["cat"].", ";
                echo "code_structure: ".$score["code_structure"].", ";
                echo "region: ".$score["region"].", ";
                echo "departement: ".$score["departement"].", ";
                echo "discipline: ".$score["discipline"].", ";
                echo "arme: ".$score["arme"].", ";
                echo "score: ".$score["score"].", ";
                echo "lieu_concours: ".$score["lieu_concours"].", ";
                echo "distance: ".$score["distance"].", ";
                echo "blason: ".$score["blason"].", ";
                echo "num_depart: ".$score["num_depart"]."</br>";
                ++$nb_scores;
            }
        }
        echo "Nombre de scores ".$nb_scores."</br>";

        return $scores;
    }


}

?>
