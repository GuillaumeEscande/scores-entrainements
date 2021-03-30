<?php namespace ffta_extractor;

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
                echo "File size must be excately 2 MB";
                die();
            }

            $file_path = $this->_file_storage_path."/".$file_name;

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
        $cpt_line = 1;
        foreach($file_contant as $line)
        {
            if($cpt_line > 2){
                $data = str_getcsv(utf8_encode($line), "\t");

                // Calcul des éléments sans mapping direct avec le fichier resultarc :

                // Saison
                $date_score = $data[19];
                $date = \DateTime::createFromFormat("d/m/Y", $date_score);
                $annee = intval($date->format("Y"));
                $mois = intval($date->format("m"));
                $jour = intval($date->format("d"));

                $saison = strval( $annee );
                if( $mois > 8 ){
                    $saison = strval( $annee + 1 );
                }

                // Région, département
                $code_structure = $data[12];
                $region = substr($code_structure, 0, 2);
                $departement = substr($code_structure, 2, 2);

                $nom = $data[4];
                $prenom = $data[5];
                $cat = $data[7];
                $arme = $data[9];
                $distance = $data[17];
                $blason = $data[18];
                // Dicsipline :
                // S = 'Salle',
                // T = TAE (I et N)
                // C = Campagne
                // 3 = 3D
                // N = Nature
                // B = Bersault
                $discipline = $data[0];
                // DAns le cas d'un TAE, il faut déterminé si c'est un TAE I ou TAE N :
                if( $discipline == "T" ){
                    if( $arme == "CL" ){
                        switch ($cat) {
                            case "S3":
                                if( $distance == "60" && $blason == "122"){
                                    $discipline = "TI";
                                } elseif ( $distance == "50" && $blason == "122"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "S2":
                                if( $distance == "70" && $blason == "122"){
                                    $discipline = "TI";
                                } elseif ( $distance == "50" && $blason == "122"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "S1":
                                if( $distance == "70" && $blason == "122"){
                                    $discipline = "TI";
                                } elseif ( $distance == "50" && $blason == "122"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "J":
                                if( $distance == "70" && $blason == "122"){
                                    $discipline = "TI";
                                } elseif ( $distance == "50" && $blason == "122"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "C":
                                if( $distance == "60" && $blason == "122"){
                                    $discipline = "TI";
                                } elseif ( $distance == "50" && $blason == "122"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "M":
                                if( $distance == "40" && $blason == "80"){
                                    $discipline = "TI";
                                } elseif ( $distance == "30" && $blason == "80"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "B":
                                if( $distance == "30" && $blason == "80"){
                                    $discipline = "TI";
                                } elseif ( $distance == "20" && $blason == "80"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            case "P":
                                if( $distance == "20" && $blason == "80"){
                                    $discipline = "TI";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            default:
                                echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                        }

                    } elseif ($arme == "CO") {
                        switch ($cat) {
                            case "S3":
                            case "S2":
                            case "S1":
                            case "J":
                            case "C":
                                if( $distance == "50" && $blason == "80"){
                                    $discipline = "TI";
                                } elseif( $distance == "50" && $blason == "122"){
                                    $discipline = "TN";
                                } else {
                                    echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                                    die();
                                }
                                break;
                            default:
                                echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                        }
                    } else {
                        echo "Erreur : Impossible Discipline impossible ".$nom." ".$prenom;
                    }

                }

                $score = array(
                    "nom" => $nom, 
                    "prenom" => $prenom, 
                    "no_licence" => $data[3], 
                    "saison" => $saison, 
                    "date_score" => $date_score, 
                    "sexe" => $data[8], 
                    "cat" => $data[7], 
                    "code_structure" => $code_structure, 
                    "region" => $region, 
                    "departement" => $departement, 
                    "discipline" => $discipline, 
                    "arme" => $data[9], 
                    "score" => $data[13], 
                    "lieu_concours" => $data[20], 
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
            }
            ++$cpt_line;
        }

        return $scores;
    }


}

?>
