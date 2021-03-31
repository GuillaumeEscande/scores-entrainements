<?php namespace ffta_extractor;



class Sorter
{

    private $_configuration;


    public function __construct( $configuration )
    {
        $this->_configuration = $configuration;
    }


    public function get_classement ( $scores, $crit ){
        $classements = array();
        // Calcul du score de chaque archer
        foreach ($scores as $score) {

            $scores_values = array_map('intval', explode(',', $score["SCORES"]));
            // Calcul du score :
            if($crit == 0){
                // Premier score
                $score["SCORE"] = $scores_values[0];
            } else if ($crit == 1){
                // Meilleur score
                rsort($scores_values);
                $score["SCORE"] = $scores_values[0];
                $score["SCORES"] = $scores_values[0];
            } else if ($crit == 2){
                // Moyenne sur 2 meilleurs scores
                rsort($scores_values);
                $selected_scores = array_slice($scores_values, 0, 2);
                $score["SCORE"] = array_sum($scores_values) / 2;
                $score["SCORES"] = implode($selected_scores, ',');
            } else if ($crit == 3){
                // Moyenne sur 2 meilleurs scores
                rsort($scores_values);
                $selected_scores = array_slice($scores_values, 0, 3);
                $score["SCORE"] = array_sum($scores_values) / 3;
                $score["SCORES"] = implode($selected_scores, ',');
            }


            array_push($classements, $score);

            $cpt++;
        }

        // Trie de la liste pas score
        usort($classements, array("ffta_extractor\Sorter", "cmp_score"));

        // Ajout du classement
        $cpt = 1;
        foreach ($classements as $classement) {
            $classement["CLASSEMENT"] = $cpt;
            $cpt++;
        }

        return $classements;
    }

    static function cmp_score($a, $b)
    {
        return ($a["SCORE"] > $b["SCORE"]);
    }
} 

?>
