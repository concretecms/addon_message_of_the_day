
<div id="ccm-message_of_the_dayBlock<?php echo intval($bID) ?>" class="ccm-message_of_the_dayBlock">
<?php  
$blocksDisplayed=0; 

echo '<div class="ccm-message_of_the_daySubBlock" >';

foreach($blockPoolBlocks as $block){ 
	
	//$displayBlock = ($animate && intval($displayCount) && $blocksDisplayed>=$displayCount)?'display:none':''; 
	
	if( ( ($blocksDisplayed)%$displayCount==0 && $blocksDisplayed>0) || (!intval($displayCount) && $blocksDisplayed>0) ){
		echo '</div>';	
		echo '<div class="ccm-message_of_the_daySubBlock" style="display:none;">'; 
	}
	
		//echo $block->getBlockTypeID().'<br>';
	
		echo '<div class="ccm-message_of_the_dayBlockWrap">';	
			$bv = new BlockView();
			$bv->render($block); 
		echo '</div>';
	
	
	$blocksDisplayed++;
	//if( intval($displayCount)>0 && $blocksDisplayed>=$displayCount ) break;
	//elseif($animate && intval($animateDisplayLimit)>0 && $blocksDisplayed>=$animateDisplayLimit) break;
}

if( $animate ){ 
	//echo '$blocksDisplayed: '.$blocksDisplayed.' $displayCount'.$displayCount.' '.($blocksDisplayed % $displayCount).'<br>';
	$numberToPrint = $displayCount - $blocksDisplayed % $displayCount;
	if(($blocksDisplayed % $displayCount)!=0) for($i=0;$i<$numberToPrint;$i++){
		echo '<div class="ccm-message_of_the_dayBlockWrap">';
			$bv = new BlockView();
			$bv->render($blockPoolBlocks[$i]); 		
		echo '</div>';
	}
}

echo '</div>';

?>
</div>