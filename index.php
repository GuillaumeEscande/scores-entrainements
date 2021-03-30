<?php 

//$server = $_SERVER['SERVER_NAME'];
function get_request($key)
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
        $val=$the_request[$key];
    }
    return $val;
}

// redirection pour https://arc-occitanie.fr/scores-entrainements/
$url_query= "https://arc-occitanie.fr/scores-entrainements/pages/query.php";

header('Location: ' . $url_query);
exit();

?>