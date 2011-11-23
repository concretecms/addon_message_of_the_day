<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$info = $controller->getCollectionData();
$bt->inc('form_setup_html.php', array('info' => $info, 'c' => $c, 'b' => $b, 'controller'=>$controller));
?>