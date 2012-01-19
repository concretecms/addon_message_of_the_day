<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php  
$info = array();
$info['blockSource'] = 'page';
$info['displayCount']=1;
$info['displayOrder']='cycle';
$info['duration']=24;
$info['animate']=1;
$info['animationTrans']=2;
$info['animateDisplayLimit']=15;

$bt->inc('form_setup_html.php', array('info' => $info, 'c' => $c, 'controller'=>$controller)); 
?>