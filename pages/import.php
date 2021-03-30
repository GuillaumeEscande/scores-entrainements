<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
header('Content-Type: text/html; charset=utf-8');

// Import de la librairie de gestion des cuts
include_once dirname(__FILE__)."/../score_manager.php";
$manager = new ffta_extractor\ScoreManager("test.json");

?>

<html>
    <head>
      <meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <meta charset="UTF-8">
      <link rel="stylesheet" href="cut.css" />
      <title>Classement | Arc Occitanie</title>
      <meta name="keywords" content="ARC, Occitanie, Comite, Regional, tir, arc, archerie, classique, poulie, compound, competition, federal, FITA, nature, field, campagne, ligue, club">
      <meta name="robots" content="index, follow">
      <meta name="classification" content="Comite Regional Tir Arc Occitanie">
    </head>
    <body>
      <table>
        <tr>
          <td>
            <a href="https://arc-occitanie.fr/">
              <img src="https://arc-occitanie.fr/images/logo/20171013_logo_crtao_all_blacks.jpg" width="150" height="150" border="0" title="Arc Occitanie"  alt="Arc Occitanie">
            </a>
          </td>
          <td>
            <h1>Import d'un fichier de résultat</h1>
          </td>
        </tr>
      </table>

      <form enctype="multipart/form-data" name="import_resultarc" method="post" action="import.php">
        <p>
          <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
          Envoyez ce fichier : <input name="result_file" type="file" />
        </p>
        <p>
          <input type="submit" name="submit_import_resultarc" value="Envoyer"></input>
        </p>
      </form>

  <?php

  if( isset( $_POST['submit_import_resultarc'] ) ){
      $manager->import('result_file');
  }

  ?>

    </body>
  </html>
