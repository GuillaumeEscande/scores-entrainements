<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
header('Content-Type: text/html; charset=utf-8');

// Import de la librairie de gestion des cuts
include_once dirname(__FILE__)."/../score_manager.php";
$manager = new ffta_extractor\ScoreManager("test.json");

function get_request($key,&$value)
{
    $val=null;
    $the_request = &$_REQUEST;
    switch($_SERVER['REQUEST_METHOD'])
    {
        case 'GET': $the_request = &$_GET; break;
        case 'POST': $the_request = &$_POST; break;
    }
    
    if (in_array($key,array_keys($the_request)))
    {
        $val=$the_request[$key]; //$_REQUEST[$key];
        $value = $val;
    }
    else if (isset($defValue))
        $val=$defValue;
    return $val;
}

// $Values[], $Names[] arrays of values and names
// the selected value is set to $curValue
function set_select($SelectionName,$curValue,$Values,$Names)
{   $strSelect="Invalid";
    if (!is_array($Values) || !is_array($Names)) return $strSelect;
    if (count($Values) != count($Names)) return $strSelect;
    $size=1;
    if (count($Values) <=3) $size =count($Values);
    $strSelect='<select name="' . $SelectionName . '">'; //'" size="'. $size . '">';
    for ($i=0;$i<count($Values);$i++)
    {   $strSel='';
        if (strcmp($Values[$i],$curValue) == 0)  $strSel=" selected ";
        $strSelect .= '<option value="' . $Values[$i] . '"' . $strSel . '>' . $Names[$i] . '</option>';
    }
    $strSelect .='</select>';
    return $strSelect;
}


/////////////////// paramétrage
$saison=2021;
get_request("saison",$saison);
$Values_saison = array("2021","2020","2019","2018","0");
$Names_saison = array("2021","2020","2019","2018","0 Toutes");

$structure = "F";
get_request("structure",$structure);
$Values_structure = array("R11", "D09", "D11", "D12", "D30", "D31", "D32", "D34", "D46", "D48", "D65", "D66", "D81", "D82", "F");
$Names_structure = array(
    'R11 CR11 Région Occitanie',
    'D09 Ari&egrave;ge',
    'D11 Aude',
    'D12 Aveyron',
    'D30 Gard',
    'D31 Haute-Garonne',
    'D32 Gers',
    'D34 Hérault',
    'D46 Lot',
    'D48 Loz&egrave;re',
    'D65 Hautes-Pyrénées',
    'D66 Pyrénées Orientales',
    'D81 Tarn',
    'D82 Tarn et Garonne',
    'F Monde');

$discipline = 'TI';
get_request("discipline",$discipline);
$Values_discipline = array('S','TI','TN','C','3','N','B');
$Names_discipline = array('18m Salle',
    'TAE International',
    'TAE National',
    'Campagne',
    '3D',
    'Nature',
    'Beursault');


$arme = 'CL';
get_request("arme",$arme);
$Values_arme = array('CL','CO','BB','AD','AC','TL');
$Names_arme = array('CL classique','CO compound - poulies','BB arc nu','AD arc droit','AC arc chasse','TL tir libre (incluant poulies avec viseur en 3D/Nature)');


$cat = 'T';
get_request("cat",$cat);
$Values_cat = array('P','B','M','C','J','S1','S2','S3','SJ','T');
$Names_cat = array('Poussin','Benjamin','Minime','Cadet','Junior','S1 senior 1','S2 senior 2','S3 senior 3','SJ senior+junior','Tous');


$sexe = 'M';
get_request("sexe",$sexe);
$Values_sexe = array('F','H', 'M');
$Names_sexe = array('Femme','Homme','Mixte');

$crit = '1';
get_request("crit",$crit);
$Values_crit = array('0','1','2','3');
$Names_crit = array('0 Premier score','1 meilleur score','2 moyenne sur 2 meilleurs scores','3 moyenne 3 meilleurs scores');


$strArg='';

?>

<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cut.css" />
    <title>Scores archers | Arc Occitanie</title>
    <meta name="keywords" content="ARC, Occitanie, Comite, Regional, tir, arc, archerie, classique, poulie, compound, competition, federal, FITA, nature, field, campagne, ligue, club">
    <meta name="robots" content="index, follow">
    <meta name="classification" content="Comite Regional Tir Arc Occitanie">
  </head>
  <body>
    <table>
      <tr>
        <td>
          <a href="https://arc-occitanie.fr/">
            <img src="https://arc-occitanie.fr/images/logo/20171013_logo_crtao_all_blacks.png" width="150" height="150" alt="retour accueil" title="retour accueil"border="0">			</a>
        </td>
        <td>
          <h1 align="left">Tirs des archers</h1>
        </td>

      </tr>
    </table>


    <form name="select_cut_form" method="get" action="query.php">
      <table>

        <tr>
          <td>
            Discipline
          </td>
          <td>
<?php
echo set_select("discipline",$discipline,$Values_discipline,$Names_discipline);
?>
          </td>
        </tr>

        <tr>
          <td>
            Arme
          </td>
          <td>
<?php
echo set_select("arme",$arme,$Values_arme,$Names_arme);
?>
          </td>
        </tr>
        <tr>
          <td>
            Sexe
          </td>
          <td>
<?php
echo set_select("sexe",$sexe,$Values_sexe,$Names_sexe);
?>
          </td>
        </tr>
        <tr>
          <td>
            Catégorie d'Age
          </td>
          <td>
<?php
echo set_select("cat",$cat,$Values_cat,$Names_cat);
?>
          </td>
        </tr>
        <tr>
          <td>
            Critère de classement
          </td>
          <td>
<?php
echo set_select("crit",$crit,$Values_crit,$Names_crit);
?>
          </td>
        </tr>


        <tr>
          <td>
            Structure
          </td>
          <td>
<?php
echo set_select("structure",$structure,$Values_structure,$Names_structure);
?>
          </td>
        </tr>

        <tr>
          <td>
            Saison
          </td>
          <td>
<?php
echo set_select("saison",$saison,$Values_saison,$Names_saison);
?>
          </td>
        </tr>

      </table>
      <p>
        <button type="submit" name="lister" value="lister">lister les archers</button>
      </p>

    </form>

<?php

$lister = $_REQUEST["lister"];
if( $lister )
{

  echo "<a href='".$strArg='?saison=' . $saison . '&structure=' . $structure . '&discipline=' . $discipline . '&arme=' . $arme . '&sexe=' . $sexe . '&cat=' . $cat . '&crit=' . $crit . '&lister=' . $lister . "'> Lien rapide <a/> </br>";
  $scores = $manager->get_scores($saison, $structure, $arme, $discipline, $cat, $sexe);
  $classements = $manager->trier_scores($scores, $crit);

  echo "</br>";
  echo "</br>";
  if ($classements)
  {
?>
    <table>
      <tr>
        <td> Nom </td>
        <td> Prénom </td>
        <td> Licence </td>
        <td> Cat. </td>
        <td> Score </td>
        <td> date </td>
        <!--  td> Classement </td -->
      </tr>
<?php

    foreach ($classements as $classement) 
    {
      echo "<tr>";

      echo "<td>";
      echo $classement['NOM'];
      echo "</td>";
      echo "<td>";
      echo $classement['PRENOM'];
      echo "</td>";
      echo "<td>";
      echo $classement['NO_LICENCE'];
      echo "</td>";
      echo "<td>";
      echo $classement['CAT'].$classement['SEXE'].$classement['ARME']; // Pierre
      echo "</td>";
      echo "<td>";
      echo $classement['SCORE'];
      echo "</td>";
      echo "<td>";
      // l'index ['CLASSEMENT'] n'est pas reconnu
      //echo $classement['CLASSEMENT']; // 
      echo $classement['DATE_SCORE']." ".$classement['LIEU_CONCOURS'];
      echo "</td>";

      echo "</tr>";
    }

?>
</table>
<?php
  }

}

?>

  </body>
</html>

