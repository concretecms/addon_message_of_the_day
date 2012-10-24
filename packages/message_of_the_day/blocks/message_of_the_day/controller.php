<?php 

class MessageOfTheDayBlockController extends BlockController { 
	
	var $pobj;
	
	protected $btTable = 'btMessageOfTheDay';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "350";
	protected $btWrapperClass = 'ccm-ui';
	
	public $blockPool_cID = 0;
	public $blockPool_arHandle = '';
	public $blockSource = 'page';
	
	public function getBlockTypeName() {
		return t("Message Of The Day");
	}
	
	public function getBlockTypeDescription() {
			return t("Display a message of the day.");
	}
	
	public function getJavaScriptStrings() {
		return array(
			'target-page-required' => t('Please choose a target page where the blocks will be pulled from.'),
			'max-duration-hours' => t('Blocks can only be displayed for a maximum of 24 hours.')
		);
	}	
	
	public function __construct($obj = null) {		
		parent::__construct($obj);
		if (!empty($this->currentBlockPool) && !is_array($this->currentBlockPool)) $this->currentBlockPool = unserialize(stripslashes($this->currentBlockPool));
	}	
	
	//returns handles for available areas
	public function getCollectionAreaData($cID){
		$db = Loader::db();
		return $db->getAll('SELECT arID, arHandle FROM Areas WHERE cID='.intval($cID));
	}
	
	public function getCollectionData(){
		$data=array();
		$data['blockSource'] = $this->blockSource;
		$data['blockPool_cID']=$this->blockPool_cID;
		$data['blockPool_arHandle']=$this->blockPool_arHandle;
		$data['displayCount']=$this->displayCount;
		$data['displayOrder']=$this->displayOrder;
		$data['duration']=$this->duration;
		$data['animate']=$this->animate;
		$data['animationType']=$this->animationType;
		$data['animationTrans']=$this->animationTrans;
		$data['animationDuration']=$this->animationDuration;
		$data['animateDisplayLimit']=$this->animateDisplayLimit;
		$data['resetCounterAt']=$this->resetCounterAt;
		return $data;
	}
	
	public function save($data){
	
		$args['blockSource'] 		= $data['blockSource'];
		$args['blockPool_cID'] 		= intval($data['blockPool_cID']);
		$args['blockPool_arHandle'] = 'Main';
		
		// set the page and area appropriately depending on what kind of place we're getting the data from
		switch($args['blockSource']) {
			case 'stack':
				$args['blockPool_arHandle'] = STACKS_AREA_NAME;
				$args['blockPool_cID'] = $data['stack_cID'];
			break;
			
			case 'scrapbook':
				$args['blockPool_arHandle'] = $data['blockPool_arHandle'];
				$args['blockPool_cID'] = Loader::helper('concrete/scrapbook')->getGlobalScrapbookPage()->getCollectionID(); 
			break;
			
			case 'page':
			default:
				$args['blockPool_arHandle'] = $data['blockPool_arHandle'];
			break;
		}
		$args['displayCount'] = intval($data['displayCount']);
		$args['displayOrder'] = ($data['displayOrder'])?$data['displayOrder']:'cycle';
		$args['duration'] = floatval($data['duration']);
		$args['animate'] = intval($data['animate']);
		$args['animationType'] = ($data['animationType'])?$data['animationType']:'fade';		
		$args['animationTrans'] = intval($data['animationTrans']);
		$args['animationDuration'] = intval($data['animationDuration']);
		$args['animateDisplayLimit'] = intval($data['animateDisplayLimit']);
		
		//clear the current blocks pool on update
		$args['currentBlockPool'] = '';
		$resetTime = strtotime($this->translateTime('resetCounterAt'));
		$nextReset = mktime(date("H",$resetTime),date("i",$resetTime),1,date('n'),date('j'),date('Y'));
		if ($nextReset<time()) {
			$nextReset = mktime(date("H",$resetTime),date("i",$resetTime),1,date('n'),date('j')+1,date('Y'));
		}
		$lastChanged = $nextReset - 24*60*60;
		$args['resetCounterAt'] = date("Y-m-d H:i",$nextReset);
		$duration = max(1,$args['duration']*60*60);
		while ($lastChanged+$duration<time()) {
			$lastChanged+=$duration;
		}
		$args['lastChanged'] = date("Y-m-d H:i:s",$lastChanged);
		parent::save($args);
	}	
	
	public function view() {
		$blockPoolBlocks=array();
		$blockPoolCollection=Page::getById( $this->blockPool_cID );
		$tmpPoolBlocks = array();
		if($blockPoolCollection){
			$area = Area::get( $blockPoolCollection, $this->blockPool_arHandle);
			if($area) $tmpPoolBlocks = $area->getAreaBlocksArray( $blockPoolCollection ); 
		}
		$time = time();
		if (is_array($this->currentBlockPool) && $time < strtotime($this->resetCounterAt) && strtotime($this->lastChanged)+($this->duration*60*60) > $time) {
			$blockPoolBlocks = array();
			foreach($this->currentBlockPool as $bID) {
				//make sure the block still exists in that page
				$block = Block::getByID($bID);
				if ($block->getBlockID()>0) {
					$blockPoolBlocks[] = Block::getByID($bID);
				}
			}
		}
		else {
			$blockPoolBlocks = $tmpPoolBlocks;
			if($this->displayOrder=='cycle') shuffle($blockPoolBlocks);
			//make sure all blocks are good
			$safeBlocks=array();
			$safeBlockIds = array();
			foreach($blockPoolBlocks as $block){
				//make sure user doesn't try to display this block, otherwise it's an infinate loop.
				if( intval($this->bID)==intval($block->getBlockID()) ) continue;
				$safeBlocks[]=$block;
			}
			$blockPoolBlocks=$safeBlocks;
			
			//limit number of blocks to display, depending on mode
			//if animation is off, just go by the displayCount
			if( !$this->animate && intval($this->displayCount)>0 ){
				$blockPoolBlocks=array_slice($blockPoolBlocks, 0, $this->displayCount );
			}elseif($this->animate && intval($this->animateDisplayLimit)>0 ){
				$blockPoolBlocks=array_slice($blockPoolBlocks, 0,$this->animateDisplayLimit );
			}
			foreach($blockPoolBlocks as $block){
				$safeBlockIds[] = $block->getBlockID();
			}
			$args['currentBlockPool'] = serialize($safeBlockIds);
			if (!is_array($this->currentBlockPool)) {
				//no need to do anything with times
			}
			elseif ($time>strtotime($this->resetCounterAt)) {
				//new day
				$lastChangedTime = strtotime($this->resetCounterAt);
				$this->resetCounterAt = $lastChangedTime+24*60*60;
				$args['resetCounterAt'] = date('Y-m-d H:i',$this->resetCounterAt);
				$args['lastChanged'] = date('Y-m-d H:i:s',$lastChangedTime);
				$this->lastChanged = $args['lastChanged'];
			}
			else {
				//new period within the day
				$lastChangedTime = strtotime($args['lastChanged']);
				$duration = max(1,$this->duration*60*60);
				while ($lastChangedTime+$duration<$time) {
					$lastChangedTime+=$duration;
				}
				$args['lastChanged'] = date('Y-m-d H:i:s',$lastChangedTime);
				$this->lastChanged = $args['lastChanged'];
			}
			$this->currentBlockPool = $args['currentBlockPool'];
			parent::save($args);
		}
		
		foreach($blockPoolBlocks as $block){
			$mBlockIds[] = $block->getBlockID();
		}
		$this->set('blockPoolBlocks', $blockPoolBlocks); 
		$this->set('displayCount', $this->displayCount);
	}
	
	
	public function on_page_view(){
		$this->set('blockPoolBlocks', $blockPoolBlocks); 
		if($this->animate){
			$htmlhelper = Loader::helper('html'); 
			$this->addHeaderItem($htmlhelper->javascript('jquery.cycle.all.min.js','message_of_the_day'));
			$animationTransMilli=intval($this->animationTrans)*1000;
			$animationDurationMilli=intval($this->animationDuration)*1000; 
			$jsString = '$(function() {$("#ccm-message_of_the_dayBlock'.$this->bID.'").cycle({ fx: "'.$this->animationType.'", timeout: '.$animationDurationMilli.', delay:  '.'1'.',speedout:  '.$animationTransMilli.', next: "#message_of_the_dayRightArrow'.$this->bID.'", prev: "#message_of_the_dayLeftArrow'.$this->bID.'" });});';
			$this->addHeaderItem('<script type="text/javascript">'.$jsString.'</script>','CONTROLLER');			
		}		
	}
	
	public function outputAutoHeaderItems(){ 
        $blockPoolBlocks=array();
        $blockPoolCollection=Page::getById( $this->blockPool_cID );
        if($blockPoolCollection) {
            $area = Area::get( $blockPoolCollection, $this->blockPool_arHandle);
            if($area) $blockPoolBlocks = $area->getAreaBlocksArray( $blockPoolCollection );
        } 
        $b = $this->getBlockObject();
		
        $bvt = new BlockViewTemplate($b);
        $headers = $bvt->getTemplateHeaderItems();
		
        //            $headers = array();
        foreach($blockPoolBlocks as $block){
            $bvt = new BlockViewTemplate($block);
            $headers = array_merge($headers,$bvt->getTemplateHeaderItems());
        }
        if (count($headers) > 0){ 
            foreach($headers as $h){ 
                $this->addHeaderItem($h); 
            }
        } 
    }
    
    public function translateTime($field, $arr = null) {
		if ($arr == null) {
			$arr = $_POST;
		}
		
		if (isset($arr[$field . '_dt'])) {
			$dt = date('Y-m-d', strtotime($arr[$field . '_dt']));
			$str = $dt . ' ' . $arr[$field . '_h'] . ':' . $arr[$field . '_m'] . ' ' . $arr[$field . '_a'];
			return date('Y-m-d H:i:s', strtotime($str));
		} else if (isset($arr[$field . '_d'])) {
			$dt = date('Y-m-d', strtotime($arr[$field . '_d']));
			return $dt;
		} else {
			return false;
		}
	}

	
	public function timeInput($prefix, $value = null, $includeActivation = false, $calendarAutoStart = true) {
		if ($value != null) {
			$dt = date('m/d/Y', strtotime($value));
			$h = date('h', strtotime($value));
			$m = date('i', strtotime($value));
			$a = date('A', strtotime($value));
		} else {
			$dt = date('m/d/Y');
			$h = date('h');
			$m = date('i');
			$a = date('A');
		}
		$id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
		$html = '';
		$disabled = false;
		if ($includeActivation) {
			if ($value) {
				$activated = 'checked';
			} else {
				$disabled = 'disabled';
			}
			
			$html .= '<input type="checkbox" id="' . $id . '_activate" class="ccm-activate-date-time" ccm-date-time-id="' . $id . '" name="' . $prefix . '_activate" ' . $activated . ' />';
		}
		//date picker //$html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_dt" name="' . $prefix . '_dt" class="ccm-input-date" value="' . $dt . '" ' . $disabled . ' /></span>';
		//Note: date is irrelevant, but a dummy date so that we don't have to rewrite all the code we borrowed from the form/date helper
		$html .= '<input type="hidden" name="' . $prefix . '_dt" value="'.$dt.'">';
		$html .= '<span class="ccm-input-time-wrapper" id="' . $id . '_tw">';
		$html .= '<select id="' . $id . '_h" name="' . $prefix . '_h" ' . $disabled . '>';
		for ($i = 1; $i <= 12; $i++) {
			if ($h == $i) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
		}
		$html .= '</select>:';
		$html .= '<select id="' . $id . '_m" name="' . $prefix . '_m" ' . $disabled . '>';
		for ($i = 0; $i <= 59; $i++) {
			if ($m == sprintf('%02d', $i)) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . sprintf('%02d', $i) . '"' . $selected . '>' . sprintf('%02d', $i) . '</option>';
		}
		$html .= '</select>';
		$html .= '<select id="' . $id . '_a" name="' . $prefix . '_a" ' . $disabled . '>';
		$html .= '<option value="AM" ';
		if ($a == 'AM') {
			$html .= 'selected';
		}
		$html .= '>AM</option>';
		$html .= '<option value="PM" ';
		if ($a == 'PM') {
			$html .= 'selected';
		}
		$html .= '>PM</option>';
		$html .= '</select>';
		$html .= '</span>';
		if ($calendarAutoStart) { 
			$html .= '<script type="text/javascript">$(function() { $("#' . $id . '_dt").datepicker({ showAnim: \'fadeIn\' }); });</script>';
		}
		// first we add a calendar input
		
		if ($includeActivation) {
			$html .=<<<EOS
			<script type="text/javascript">$("#{$id}_activate").click(function() {
				if ($(this).get(0).checked) {
					$("#{$id}_dw input").each(function() {
						$(this).get(0).disabled = false;
					});
					$("#{$id}_tw select").each(function() {
						$(this).get(0).disabled = false;
					});
				} else {
					$("#{$id}_dw input").each(function() {
						$(this).get(0).disabled = true;
					});
					$("#{$id}_tw select").each(function() {
						$(this).get(0).disabled = true;
					});
				}
			});
			</script>
EOS;
			
		}
		return $html;
	
	}

 	
}
	
?>