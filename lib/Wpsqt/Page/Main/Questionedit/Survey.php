<?php
require_once WPSQT_DIR.'lib/Wpsqt/Page/Main/Questionedit.php';
	/**
	 * Handles editing the question.
	 * 
	 * @author Iain Cambridge
	 * @copyright Fubra Limited 2010-2011, all rights reserved.
  	 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
  	 * @package WPSQT
	 */

class Wpsqt_Page_Main_Questionedit_Survey extends Wpsqt_Page_Main_Questionedit {

	public function init(){
		parent::init();
		$this->_type = "survey";	
	}	
	
}
