<?php 
global $c;

$formPageSelector = Loader::helper('form/page_selector');

$areasData =  $controller->getCollectionAreaData( intval($info['blockPool_cID']) );  // array();
 
/* 
if($info['blockPool_cID']){
	$blockPoolPage=Page::getById(intval($info['blockPool_cID']));
	if($blockPoolPage) $blockPoolPageName=$blockPoolPage->getCollectionName();
}
*/ 

$scrapbookHelper = Loader::helper('concrete/scrapbook'); 
$globalScrapbookPage=$scrapbookHelper->getGlobalScrapbookPage(); 
$globalScrapbookPageId=$globalScrapbookPage->getCollectionId(); 

?>


<input name="message_of_the_dayServices" type="hidden" value="<?php  echo addslashes(View::url('/tools/blocks/message_of_the_day/services')) ?>" />

<strong><?php echo t('Display blocks from:')?></strong><br>

<input name="bookPool_fromScrapbook" type="radio" value="0" <?php echo ($globalScrapbookPageId!=intval($info['blockPool_cID']))?'checked':''?> /> Site Page &nbsp; 
<input id="bookPool_fromScrapbookOnRadio" name="bookPool_fromScrapbook" type="radio" value="<?php echo intval($globalScrapbookPageId) ?>" <?php echo ($globalScrapbookPageId==intval($info['blockPool_cID']))?'checked':''?>  /> Scrapbook <br /> 

<div id="ccm-message_of_the_day-page-selector" style="display:<?php echo ($globalScrapbookPageId!=$info['blockPool_cID'])?'block':'none'?>" >
	<?php echo  $formPageSelector->selectPage('blockPool_cID', $info['blockPool_cID'], 'ccm_message_of_the_daySelectSitemapNode'); ?>  
</div>

&nbsp;<br/> 

<strong><?php echo t('From the area:')?></strong><br>
<div> 
	<select id="blockPool_arHandle" name="blockPool_arHandle">
		<?php  foreach($areasData as $areaData){ ?>
			<option value="<?php echo addslashes($areaData['arHandle']) ?>" 
			   <?php echo ($areaData['arHandle']==$info['blockPool_arHandle'])?'selected':''?>><?php echo $areaData['arHandle'] ?></option>
		<?php  } ?>
	</select>
</div>
&nbsp;<br/> 

<strong><?php echo t('Number of blocks to display')?></strong><br>
<div>
	<input name="displayCount" type="text" value="<?php echo intval($info['displayCount']) ?>" size="3">
</div>
&nbsp;<br/> 

<strong><?php echo t('Order Blocks')?></strong><br>
<div>
	<input name="displayOrder" type="radio" value="cycle" <?php echo ($info['displayOrder']=='cycle')?'checked':''?>>
	<?php echo t('Randomly') ?>&nbsp; 
	<input name="displayOrder" type="radio" value="display_order" <?php echo ($info['displayOrder']=='display_order')?'checked':''?>>
	<?php echo t('By Display Order') ?>
</div>
&nbsp;<br/>
<strong><?php echo t('Display Blocks For')?></strong><br>
	<div>
		<input name="duration" type="text" value="<?php echo floatval($info['duration']) ?>" size="3"> Hours
	</div>&nbsp;<br/>
<strong><?php echo t('Reset timer every day at')?></strong><br>
	<div>
		<?php echo $this->controller->timeInput("resetCounterAt",$info['resetCounterAt']); ?>
	</div>&nbsp;<br/>

<strong><?php echo t('Animate blocks')?></strong><br>
<div> 
	<input id="ccm-message_of_the_dayAnimateCheckbox"  name="animate" type="checkbox" value="1" <?php echo (intval($info['animate']))?'checked':''?> /> Yes&nbsp; 
</div>&nbsp;<br/>

<div id="ccm-message_of_the_dayAnimationOptions" style=" <?php echo (intval($info['animate']))?'':'display:none' ?>" >

	<strong><?php echo t('Display Time')?></strong><br>
	<div>
		<input name="animationDuration" type="text" value="<?php echo intval($info['animationDuration']) ?>" size="3">
	</div>&nbsp;<br/>

	<strong><?php echo t('Transition Time')?></strong><br>
	<div>
		<input name="animationTrans" type="text" value="<?php echo intval($info['animationTrans']) ?>" size="3">
	</div>&nbsp;<br/>

	<strong><?php echo t('Transition Style')?></strong><br>
	<div>
		<?php  
		$form = Loader::helper('form');
		$animationTypes = array("fade" => "Fade", "scrollDown" => "Scroll Down", "scrollUp" => "Scroll Up", "shuffle" => "Shuffle", "cover" => "Cover", "zoom" => "Zoom", "blindZ" => "Blindz");
		echo $form->select('animationType', $animationTypes, $info['animationType']);
		?>
	</div>&nbsp;<br/>	
	
	<strong><?php echo t('Size of animation pool') ?></strong><br>
	<div>
		<input name="animateDisplayLimit" type="text" value="<?php echo intval($info['animateDisplayLimit']) ?>" size="4">
	</div>&nbsp;<br/>	
</div>