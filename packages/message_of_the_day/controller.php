<?php    

defined('C5_EXECUTE') or die(_("Access Denied."));

class MessageOfTheDayPackage extends Package {

	protected $pkgHandle = 'message_of_the_day';
	protected $appVersionRequired = '5.5';
	protected $pkgVersion = '1.1';  
	
	public function getPackageName() {
		return t("Message of the day"); 
	}	
	
	public function getPackageDescription() {
		return t("Display the message of the day.");
	}
	
	public function install() {
		$pkg = parent::install();
		
		// install block
		BlockType::installBlockTypeFromPackage('message_of_the_day', $pkg);
		$this->addScrapbookContent($pkg); 
	}
	
	private function addScrapbookContent($pkg) {	
		try {
			$xml = simplexml_load_file($pkg->getPackagePath() .'/seed_messages.xml','SimpleXMLElement', LIBXML_NOCDATA);
			$u = new User();
			
			Loader::model('stack');
			
			foreach($xml->scrapbook as $sb) {
				$title = t('Message of the day - ').$sb->title;
				
				Stack::addStack($title);
				$stack = Stack::getByName($title);
				
				foreach($sb->content as $content) {
					$bt = BlockType::getByHandle('content');
					$data = array();
					$data['uID'] = $u->getUserID();
					$data['content'] = $content;
					$stackArea = Area::get($stack,STACKS_AREA_NAME);
					$stack->addBlock($bt, $stackArea, $data);
				}
			}
		} catch ( Exception $e ) {
			throw $e;
		}
	}

}