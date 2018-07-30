<?php
//Set stream options
$context = stream_context_create(array('http' => array('ignore_errors' => true)));
if(!isset($_GET['tfa_next'])) {
$qs = ' ';
if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])){$qs='?'.$_SERVER['QUERY_STRING'];};
echo file_get_contents('https://seiu503.tfaforms.net/rest/forms/view/10'.$qs);
} else {
echo file_get_contents('https://seiu503.tfaforms.net/rest'.$_GET['tfa_next'],false,$context);
}
?>
