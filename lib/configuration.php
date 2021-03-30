<?php namespace ffta_extractor;

class Configuration
{
    private $_fichier = NULL;
    private $_config = NULL;

    public function __construct($fichier)
    {
        $this->_fichier = $fichier;

        $jsonStr = file_get_contents($fichier);
        $this->_config = json_decode($jsonStr, true);

    }

    public function get_configuration_bdd( $data ){
        return $this->_config["bdd"][$data];
    }

    public function get_configuration_log( $data ){
        return $this->_config["log"][$data];
    }

}

?>
