<?php    

defined('C5_EXECUTE') or die(_("Access Denied."));

class MessageOfTheDayPackage extends Package {

	protected $pkgHandle = 'message_of_the_day';
	protected $appVersionRequired = '5.5';
	protected $pkgVersion = '1.2dev';
	
	public function getPackageName() {
		return t("Message of the day"); 
	}	
	
	public function getPackageDescription() {
		return t("Display the message of the day.");
	}
	
	public function install($options = array()) {
		$pkg = parent::install();
		
		// install block
		BlockType::installBlockTypeFromPackage('message_of_the_day', $pkg);
		$this->addStackContent($options);
	}
	
	protected function addStackContent($options) {
		$pkg = Package::getByHandle('message_of_the_day');
		if(version_compare(Config::get('SITE_APP_VERSION'), '5.6', 'lt')) {
			Loader::library('content/importer');
		}
		$contentImporter = new ContentImporter();
		if($options['mark-twain'] === 'yes') {
			$contentImporter->importContentFile(dirname(__FILE__).'/mark_twain.xml');
		}
		if($options['scripture'] === 'yes') {
			$contentImporter->importContentFile(dirname(__FILE__).'/scripture.xml');
		}
	}

}