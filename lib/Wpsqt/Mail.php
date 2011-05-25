<?php
require_once WPSQT_DIR.'lib/Wpsqt/Tokens.php';

/**
 * Handle sending the notification emails. 
 * 
 * @author Iain Cambridge
 * @copyright Fubra Limited 2010-2011, all rights reserved.
 * @license http://www.gnu.org/licenses/gpl.html GPL v3 
 * @package WPSQT
 */
class Wpsqt_Mail {
	
	/**
	 * Sends the notificatione mail.
	 * 
	 * @since 2.0
	 */
	public static function sendMail(){
		
		$quizName = $_SESSION['wpsqt']['current_id'];
		
		$objTokens = Wpsqt_Tokens::getTokenObject();
		$objTokens->setDefaultValues();
		
		$emailMessage = $_SESSION['wpsqt'][$quizName]['details']['email_template'];
		
		if ( empty($emailMessage) ){
			$emailMessage = get_option('wpsqt_email_template');
		}
		
		if ( empty($emailMessage) ){
			
			$emailMessage  = 'There is a new result to view'.PHP_EOL.PHP_EOL;
			$emailMessage .= 'Person Name : %USER_NAME%'.PHP_EOL;
			$emailMessage .= 'IP Address : %IP_ADDRESS%'.PHP_EOL;
			$emailMessage .= 'Result can be viewed at %RESULT_URL%'.PHP_EOL;
			
		}
		
		$emailMessage = $objTokens->doReplacement($emailMessage);
									
		$quizDetails = $_SESSION['wpsqt'][$quizName]['details'];
		$emailTemplate = (empty($quizDetails['email_template'])) ? get_option('wpsqt_email_template'):$quizDetails['email_template'];
		$fromEmail = ( get_option('wpsqt_from_email') ) ?  get_option('wpsqt_from_email') : get_option('admin_email');
		
		$role = get_option('wpsqt_email_role');
		$personName = ( isset($_SESSION['wpsqt'][$quizName]['person']['user_name']) && !empty($_SESSION['wpsqt'][$quizName]['person']['user_name']) ) ? $_SESSION['wpsqt'][$quizName]['person']['user_name'] : 'Anonymous';
		
		if ( !empty($role) && $role != 'none' ){
			$this_role = "'[[:<:]]".$role."[[:>:]]'";
	  		$query = "SELECT * 
	  				  FROM ".$wpdb->users." 
	  				  WHERE ID = ANY 
	  				  	(
	  				  		SELECT user_id 
	  				  		FROM ".$wpdb->usermeta." 
	  				  		WHERE meta_key = 'wp_capabilities' 
	  				  			AND meta_value RLIKE ".$this_role."
						) 
	  				  ORDER BY user_nicename ASC LIMIT 10000";
	  		$users = $wpdb->get_results($query,ARRAY_A);
	  		$emailList = array();
	  		foreach($users as $user){
	  			$emailList[] = $user['user_email'];
	  		}
		}
		
		if ( isset($_SESSION['wpsqt'][$quizName]['details']['send_user']) 
		  && $_SESSION['wpsqt'][$quizName]['details']['send_user'] == "yes" ) {
			$emailList[] = $_SESSION['wpsqt'][$quizName]['person']['email'];
		}
		
		
		if ( !isset($emailList) || empty($emailList) ){
			$emailAddress = get_option('wpsqt_contact_email');
			if ( !empty($_SESSION['wpsqt'][$quizName]['details']['notification_email'])  ){
				$emailAddress = $_SESSION['wpsqt'][$quizName]['details']['notification_email'];
			}
			$emailList = explode(',', $emailAddress);
		}

		$emailSubject  = 'There is a new result!';
		$headers = 'From: WPSQT Bot <'.$fromEmail.'>' . "\r\n";

		foreach( $emailList  as $emailAddress ){
			wp_mail($emailAddress,'WPSQT Notification',$emailMessage,$headers);
		}	
	}
	
}