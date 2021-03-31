<?php namespace ffta_extractor;

include_once "lib/configuration.php";
include_once "lib/bdd.php";
include_once "lib/importer.php";
include_once "lib/logger.php";
include_once "lib/sorter.php";

class ScoreManager
{

    private $_configuration = NULL;
    private $_bdd = NULL;
    private $_importer = NULL;
    private $_logger = NULL;
    private $_sorter = NULL;

    public function __construct($conf_file_name)
    {
        $this->_configuration = new Configuration( dirname(__FILE__)."/conf/".$conf_file_name );

        $this->_logger = new Logger( $this->_configuration );
        $this->_bdd = new BDD( $this->_configuration );
        $this->_importer = new Importer( $this->_configuration );
        $this->_sorter = new Sorter( $this->_configuration );


    }



    public function import ( $request_id ){
        $this->_bdd->create_table_scores();
        $file_path = $this->_importer->import_file( $request_id );
        $scores = $this->_importer->analyse_file( $file_path );
        $this->_bdd->import_score_bulk($scores);
    }

    public function print_logs ( $div=false ){
        return $this->_logger->print_logs( $div );
    }

    public function get_classement ( $saison, $structure, $arme, $discipline, $cat, $sexe, $crit ){
        $scores = $this->_bdd->get_scores( $saison, $structure, $arme, $discipline, $cat, $sexe );
        return $this->_sorter->get_classement( $scores, $crit );
    }


}

?>
