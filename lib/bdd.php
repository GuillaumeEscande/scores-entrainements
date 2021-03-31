<?php namespace ffta_extractor;

use \PDO;

class BDD
{

    private $_configuration;
    private $_pdo;
    public function __construct( $configuration )
    {
        $this->_configuration = $configuration;

        if(!class_exists('SQLite3'))
          die("SQLite 3 NOT supported.");

        try{

            $LIB_HOME = dirname(__FILE__).'/..';
            $this->_pdo = new PDO(
                str_replace('$LIB_HOME', $LIB_HOME, $this->_configuration->get_configuration_bdd("url")),
                $this->_configuration->get_configuration_bdd("login"),
                $this->_configuration->get_configuration_bdd("password") );
                $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_pdo->query("PRAGMA synchronous = OFF");
                $this->_pdo->query("PRAGMA journal_mode = MEMORY");
        } catch(Exception $e) {
            echo "Impossible d'accéder à la base de données ".$this->_configuration->get_configuration_bdd("url")." : ".$e->getMessage();
            die();
        }
    }

    public function get_PDO(){
        return $this->_pdo;
    }

    public function create_table_scores(){

        //$this->_pdo->query("DROP TABLE IF EXISTS SCORES") or die("Error to DROP SCORES");

        $query = 'CREATE TABLE IF NOT EXISTS SCORES(
            "NOM" TEXT NOT NULL,
            "PRENOM" TEXT NOT NULL,
            "NO_LICENCE" TEXT NOT NULL,
            "SAISON" INT NOT NULL,
            "DATE_SCORE" TEXT NOT NULL,
            "SEXE" TEXT NOT NULL,
            "CAT" TEXT NOT NULL,
            "CODE_STRUCTURE" text NOT NULL,
            "REGION" text NOT NULL,
            "DEPARTEMENT" text NOT NULL,
            "DISCIPLINE" text NOT NULL,
            "ARME" text NOT NULL,
            "SCORE" int NOT NULL,
            "LIEU_CONCOURS" text NOT NULL,
            "DISTANCE" int NOT NULL,
            "BLASON" int NOT NULL,
            "NUM_DEPART" int NOT NULL,
            PRIMARY KEY ("NO_LICENCE", "DATE_SCORE", "DISCIPLINE", "ARME", "LIEU_CONCOURS", "NUM_DEPART") )';
        $this->_pdo->query($query) or die("Error to CREATE SCORES");
        echo "Creation table SCORES <br>";
    }


    /*
    Ici $scores est un tableau de tableau
    Chaque ligne de scores doit être un tableau à 19 éléments ayant la même structure que la table de BDD SCORES
    */
    public function import_score_bulk($scores){
        $query_prepare = "INSERT INTO SCORES VALUES(
            :NOM,
            :PRENOM,
            :NO_LICENCE,
            :SAISON,
            :DATE_SCORE,
            :SEXE,
            :CAT,
            :CODE_STRUCTURE,
            :REGION,
            :DEPARTEMENT,
            :DISCIPLINE,
            :ARME,
            :SCORE,
            :LIEU_CONCOURS,
            :DISTANCE,
            :BLASON,
            :NUM_DEPART)";

        $stmt = $this->_pdo->prepare($query_prepare);

        foreach( $scores as $score ){ 
            $stmt->bindValue(":NOM", $score['nom']);
            $stmt->bindValue(":PRENOM", $score['prenom']);
            $stmt->bindValue(":NO_LICENCE", $score['no_licence']);
            $stmt->bindValue(":SAISON", $score['saison']);
            $stmt->bindValue(":DATE_SCORE", $score['date_score']);
            $stmt->bindValue(":SEXE", $score['sexe']);
            $stmt->bindValue(":CAT", $score['cat']);
            $stmt->bindValue(":CODE_STRUCTURE", $score['code_structure']);
            $stmt->bindValue(":REGION", $score['region']);
            $stmt->bindValue(":DEPARTEMENT", $score['departement']);
            $stmt->bindValue(":DISCIPLINE", $score['discipline']);
            $stmt->bindValue(":ARME", $score['arme']);
            $stmt->bindValue(":SCORE", $score['score']);
            $stmt->bindValue(":LIEU_CONCOURS", $score['lieu_concours']);
            $stmt->bindValue(":DISTANCE", $score['distance']);
            $stmt->bindValue(":BLASON", $score['blason']);
            $stmt->bindValue(":NUM_DEPART", $score['num_depart']);
            try{
                $result = $stmt->execute();
            }catch (\PDOException $e){
                echo " : ".$e->getMessage( )."<br/>\n";
            }
        }
    }


    public function get_scores ( $saison, $structure, $arme, $discipline, $cat, $sexe )
    {
        $query_scores = "SELECT NOM, PRENOM, NO_LICENCE, CAT, SEXE, ARME, GROUP_CONCAT(SCORE, ',') as SCORES FROM SCORES WHERE ";

        if($saison >0){
            $query_scores .= "SAISON=:SAISON AND ";
        }

        if(strpos($structure, "D") !== false){
            $query_scores .= "DEPARTEMENT=:DEPARTEMENT AND ";
        }

        if(strpos($structure, "R") !== false){
            $query_scores .= "REGION=:REGION AND ";
        }

        if($arme){
            $query_scores .= "ARME=:ARME AND ";
        }

        if($discipline){
            $query_scores .= "DISCIPLINE=:DISCIPLINE AND ";
        }

        if ($cat == 'SJ') {
            $query_scores .= "CAT IN ('J','S1','S2','S3') AND ";
        } elseif( $cat && ($cat != "T") ){
            $query_scores .= "CAT=:CAT AND ";
        }

        if($sexe && $sexe != "M"){
            $query_scores .= "SEXE=:SEXE AND ";
        }
        $query_scores .= "1 GROUP BY NO_LICENCE ORDER BY NO_LICENCE";

        $sth_scores = $this->_pdo->prepare($query_scores);

        if($saison>0){
            $sth_scores->bindValue(":SAISON", $saison);
        }

        if(strpos($structure, "D") !== false ){
            $sth_scores->bindValue(":DEPARTEMENT", substr($structure, 1));
        }

        if(strpos($structure, "R") !== false ){
            $sth_scores->bindValue(":REGION", substr($structure, 1));
        }

        if($arme){
            $sth_scores->bindValue(":ARME", $arme);
        }

        if($discipline){
            $sth_scores->bindValue(":DISCIPLINE", $discipline);
        }

        if($cat && $cat != "T" && $cat != "SJ"){
            $sth_scores->bindValue(":CAT", $cat);
        }

        if($sexe && $sexe != "M"){
            $sth_scores->bindValue(":SEXE", $sexe);
        }

        $sth_scores->execute();
        $result = $sth_scores->fetchAll();

        return $result;
    }
}

?>
