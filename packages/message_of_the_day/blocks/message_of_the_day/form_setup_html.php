<?php 
$c = $controller->getCollectionObject();

$formPageSelector = Loader::helper('form/page_selector');
$form = Loader::helper('form');

$areasData =  $controller->getCollectionAreaData( intval($info['blockPool_cID']) );  // array();

$scrapbookHelper = Loader::helper('concrete/scrapbook'); 
$available_scrapbooks = $scrapbookHelper->getAvailableScrapbooks();
if(is_array($available_scrapbooks)) {
	$scrapbooks = array();
	foreach($available_scrapbooks as $sb) {
		$scrapbooks[$sb['arHandle']] = $sb['arHandle'];
	}
	$available_scrapbooks = $scrapbooks; unset($scrapbooks);
}


$globalScrapbookPage = $scrapbookHelper->getGlobalScrapbookPage();
$globalScrapbookPageID = $globalScrapbookPage->getCollectionId();
$haz_scrapbook = ($globalScrapbookPageID > 0);

$blockSource = $info['blockSource'];

if (!isset($blockSource) || !strlen($blockSource)) {
	if($info['blockPool_cID'] == $globalScrapbookPageID) {
		$blockSource = 'scrapbook'; // set to scrapbook for legacy
	}
}

Loader::model('stack/list');
$stackList = new StackList(); 
$stackList->filterByUserAdded();
foreach($stackList->get() as $stack) {
	//if ($stack->getStackTypeExportText() != 'global_area') { //option to avoid global areas
	$available_stacks[$stack->getCollectionID()] = $stack->getCollectionName();
}


?>
<fieldset class="form-stacked">
<input name="randomizerServices" type="hidden" value="<?php  echo addslashes(View::url('/tools/blocks/randomizer/services')) ?>" />
<?php if ($haz_scrapbook && $blockSource == 'scrapbook') {?>
	<div class="block-message warning alert-message">
		<p><?php echo t('Scrapbooks have been replaced with <a href="%s">stacks</a>.',View::url('/dashboard/blocks/stacks'))?></p>
	</div>
<?php } ?>

<div class="clearfix">
	<label><?php echo t('Display blocks from:')?></label>
		<div class="input">
			<ul class="inputs-list">
				<li>	
					<label><input id="blockSource-page" name="blockSource" type="radio" value="page" <?php echo ($blockSource =='page')?'checked':''?> /> <span><?php echo t('Site Page')."&nbsp;";?></span></label>
				</li>
				<li>
					<label><input id="blockSource-stack" name="blockSource" type="radio" value="stack" <?php echo ($blockSource == 'stack')?'checked':''?>  /> <span><?php echo t('Stacks'). '<br />'; ?></span></label>
				</li>

				<?php if ($haz_scrapbook) { ?>
				<li>
					<label><input id="blockSource-scrapbook" name="blockSource" type="radio" value="scrapbook" <?php echo ($blockSource == 'scrapbook')?'checked':''?>  /> <span><?php echo t('Scrapbook'); ?></span></label>
				</li>
				<?php }?>
			</ul>
	</div>
</div>

<div id="ccm-randomizer-page-selector" class="ccm-blockSource-option" style="display:<?php echo ($blockSource == 'page')?'block':'none'?>" >
	<div class="clearfix">
		<?php echo  $formPageSelector->selectPage('blockPool_cID', $info['blockPool_cID'], 'ccm_randomizerSelectSitemapNode'); ?>  
	</div>
	
	<div class="clearfix">
		<label for="blockPool_arHandle"><?php echo t('From the area:')?></label>
		<div class="input"> 
			<select id="blockPool_arHandle" name="blockPool_arHandle">
			<?php  foreach($areasData as $areaData){ ?>
				<option value="<?php echo addslashes($areaData['arHandle']) ?>" 
				   <?php echo ($areaData['arHandle']==$info['blockPool_arHandle'])?'selected':''?>><?php echo $areaData['arHandle'] ?></option>
			<?php  } ?>
			</select>
		</div>
	</div>
</div>

<div class="clearfix ccm-blockSource-option" id="ccm-randomizer-stack-list" class="ccm-blockSource-option" style="display:<?php echo ($blockSource == 'stack')?'block':'none'?>">
	<label><?php echo t('Use this stack:') ?></label>
	<div class="input">
		<?php if(is_array($available_stacks) && count($available_stacks)) {?>
			<?php echo $form->select('stack_cID',$available_stacks, $info['blockPool_cID']); ?>
		<?php } else { ?>
			<div class="block-message warning alert-message"><?php echo t('You haven\'t created any stacks yet');?></div>
		<?php } ?>
	</div>
</div>
<?php if ($haz_scrapbook) { ?>
<div class="clearfix ccm-blockSource-option" id="ccm-randomizer-scrapbook-list" style="display:<?php echo ($blockSource == 'scrapbook'?'block':'none')?>">
	<label><?php echo t('Use this scrapbook:') ?></label>
	<div class="input">
		<?php echo $form->select('blockPool_arHandle',$available_scrapbooks, $info['blockPool_arHandle']); ?>
	</div>
</div>
<? } ?>

<div class="clearfix">
	<label><?php echo t('Number of blocks to display')?></label>
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
				<label><input id="ccm-randomizerAnimateCheckbox"  name="animate" type="checkbox" value="1" <?php echo (intval($info['animate']))?'checked':''?> /></label>
			</li>
		</ul>
	</div>
</div>

<div id="ccm-randomizerAnimationOptions" style=" <?php echo (intval($info['animate']))?'':'display:none' ?>" >
	<div class="clearfix">
		<label><?php echo t('Display Time')?></label>
		<div class="input">
			<input name="animationDuration" type="text" value="<?php echo intval($info['animationDuration']) ?>" size="3">
		</div>
	</div>
	
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
