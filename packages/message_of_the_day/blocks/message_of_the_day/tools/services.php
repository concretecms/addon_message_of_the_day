<?php 

$areasData = MessageOfTheDayBlockController::getCollectionAreaData(intval($_REQUEST['cID']));

$selectedArea=$_REQUEST['selectedArea'];

//var_dump($areasData);

foreach($areasData as $areaData){ ?>
<option value="<?php echo addslashes($areaData['arHandle']) ?>"  <?php echo ($areaData['arHandle']==$selectedArea)?'selected':''?>><?php echo $areaData['arHandle'] ?></option>
		
<?php  } ?>