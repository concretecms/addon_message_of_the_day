<?php  $c = $controller->getCollectionObject();

$formPageSelector = Loader::helper('form/page_selector');
$form = Loader::helper('form');

$areasData =  $controller->getCollectionAreaData( intval($info['blockPool_cID']) );  // array();

//$scrapbookHelper = Loader::helper('concrete/scrapbook'); 
//$globalScrapbookPage=$scrapbookHelper->getGlobalScrapbookPage(); 
//$globalScrapbookPageId=$globalScrapbookPage->getCollectionId(); 

$scrapbookHelper = Loader::helper('concrete/scrapbook'); 
$globalScrapbookPage=$scrapbookHelper->getGlobalScrapbookPage(); 
$globalScrapbookPageID=$globalScrapbookPage->getCollectionId();
$haz_scrapbook = ($globalScrapbookPageID > 0);
if ($haz_scrapbook) {
	$available_scrapbooks = $controller->getCollectionAreaData(intval($globalScrapBookPageID));
}

Loader::model('stack/list');
$stackList = new StackList(); 
$stackList->filterByUserAdded();
foreach($stackList->get() as $stack) {
	//if ($stack->getStackTypeExportText() != 'global_area') { //option to avoid global areas
	$available_stacks[$stack->getCollectionID()] = $stack->getCollectionName();
}
$using_stacks = $info['using_stacks'];

?>

<fieldset>
<input name="message_of_the_dayServices" type="hidden" value="<?php  echo addslashes(View::url('/tools/blocks/message_of_the_day/services')) ?>" />
<?php if ($haz_scrapbook && $globalScrapbookPageID == $info['blockPool_cID']) {?>
	<div class="block-message warning alert-message"><p>Scrapbooks have been replaced with stacks, which are better.</p><p> Search "stacks" for more info.</p></div>
<?php } ?>
<div class="clearfix">
	<label><?php echo t('Display blocks from:')?></label>
	<div class="input">
		<ul class="inputs-list">
			<li>	
				<label><input name="using_stacks" type="radio" value="0" <?php echo ($using_stacks)?'':'checked'?> /> <span><?php echo t('Site Page')."&nbsp;";?></span></label>
			</li>
			<li>
				<label><input id="using_stacksOn" name="using_stacks" type="radio" value="1" <?php echo ($using_stacks)?'checked':''?>  /> <span><?php echo t('Stacks'). '<br />'; ?></span></label>
			</li>
			<?php if ($haz_scrapbook) { ?>
			<li>
				<label><input id="using_scrapbookOn" name="using_stacks" type="radio" value="<?php echo $globalScrapBookPageID?>" <?php echo ($globalScrapBookPageID == $info['blockPool_cID'])?'checked':''?>  /> <span><?php echo t('Scrapbook'); ?></span></label>
			</li>
			<?php }?>
		</ul>
	</div>
</div>

<div style="display:<?php echo ($globalScrapbookPageId!=$info['blockPool_cID'])?'block':'none'?>" class="clearfix" id="ccm-motd-page-selector">
	<div class="input">
		<?php echo  $formPageSelector->selectPage('blockPool_cID', $info['blockPool_cID'], 'ccm_message_of_the_daySelectSitemapNode'); ?> 
	</div>
</div>

<div class="clearfix" id="stack-list" style="display:<?php echo ($using_stacks)?'block':'none'?>">
	<label><?php echo t('Use this stack:') ?></label>
	<div class="input">
		<?php echo $form->select('stack_cID',$available_stacks, $info['blockPool_cID']); ?>
	</div>
</div>

<?php if ($haz_scrapbook) { ?>
<div class="clearfix" id="scrapbook-list" style="display:<?php echo ($globalScrapBookPageID == $info['blockPool_cID'])?'block':'none'?>">
	<label><?php echo t('Use this scrapbook:') ?></label>
	<div class="input">
		<?php echo $form->select('blockPool_arHandle',$available_scrapbooks, $info['blockPool_arHandle']); ?>
</div>
<? } ?>

<div class="clearfix">
	<label><?php echo t('From the area:')?></label>
	<div class="input"> 
		<select id="blockPool_arHandle" name="blockPool_arHandle">
			<?php  foreach($areasData as $areaData){ ?>
				<option value="<?php echo addslashes($areaData['arHandle']) ?>" 
				   <?php echo ($areaData['arHandle']==$info['blockPool_arHandle'])?'selected':''?>><?php echo $areaData['arHandle'] ?></option>
			<?php  } ?>
		</select>
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Number of blocks to display:')?></label>
	<div class="input">
		<input name="displayCount" type="text" value="<?php echo intval($info['displayCount']) ?>" size="3">
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Order Blocks')?></label>
	<div class="input">
		<ul class="inputs-list">
			<li>
				<label><input name="displayOrder" type="radio" value="cycle" <?php echo ($info['displayOrder']=='cycle')?'checked':''?>>
				<span><?php echo t('Randomly') ?></span></label>
			</li>
			<li>
				<label><input name="displayOrder" type="radio" value="display_order" <?php echo ($info['displayOrder']=='display_order')?'checked':''?>>
				<span><?php echo t('By Display Order') ?></span></label>
			</li>
		</ul>
	</div>
</div>

<div class="clearfix">
	<label for="duration"><?php echo t('Hours to Display Blocks For')?></label>
	<div class="input">
		<input name="duration" type="text" value="<?php echo floatval($info['duration']) ?>" size="3">
	</div>
</div>	

<div class="clearfix">
	<label><?php echo t('Reset timer every day at')?></label>
	<div class="input">
		<?php echo $this->controller->timeInput("resetCounterAt",$info['resetCounterAt']); ?>
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Animate blocks')?></label>
	<div class="input">
		<ul class="inputs-list">
			<li>
				<input id="ccm-message_of_the_dayAnimateCheckbox"  name="animate" type="checkbox" value="1" <?php echo (intval($info['animate']))?'checked':''?> /> <span>Yes</span>
			</li>
		</ul>
	</div>
</div>

<div id="ccm-message_of_the_dayAnimationOptions" style=" <?php echo (intval($info['animate']))?'':'display:none' ?>">
	<div id="clearfix">
		<label><?php echo t('Display Time')?></label>
		<div class="input">
			<input name="animationDuration" type="text" value="<?php echo intval($info['animationDuration']) ?>" size="3">
		</div>
	</div>
<br />
<div class="clearfix">
	<label><?php echo t('Transition Time')?></label>
	<div class="input">
		<input name="animationTrans" type="text" value="<?php echo intval($info['animationTrans']) ?>" size="3">
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Transition Style')?></label>
	<div class="input">
		<?php  
		$form = Loader::helper('form');
		$animationTypes = array("fade" => "Fade", "scrollDown" => "Scroll Down", "scrollUp" => "Scroll Up", "shuffle" => "Shuffle", "cover" => "Cover", "zoom" => "Zoom", "blindZ" => "Blindz");
		echo $form->select('animationType', $animationTypes, $info['animationType']);
		?>
	</div>
</div>

<div class="clearfix">
	<label><?php echo t('Size of animation pool') ?></label>
	<div class="input">
		<input name="animateDisplayLimit" type="text" value="<?php echo intval($info['animateDisplayLimit']) ?>" size="4">
	</div>
</div>
</div>
</fieldset>