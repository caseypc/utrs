<?php
ini_set('error_reporting', -1);
require_once('exceptions.php');

/**
 * This class contains information relevant to a single unblock appeal.
 * 
 */
class Appeal{
	
	/**
	 * The appeal is new and has not yet been addressed
	 */
	public static $STATUS_NEW = 'NEW';
	/**
	 * A response has been sent to the user, and a reply is expected
	 */
	public static $STATUS_AWAITING_USER = 'AWAITING_USER';
	/**
	 * The user has replied to a response, and the appeal is ready for further
	 * action from the handling administrator
	 */
	public static $STATUS_AWAITING_ADMIN = 'AWAITING_ADMIN';
	/**
	 * The appeal needs to be reviewed by a checkuser before it can proceed
	 */
	public static $STATUS_AWAITING_CHECKUSER = 'AWAITING_CHECKUSER';
	/**
	 * The appeal needs to be reviewed by OPP before it can proceed
	 */
	public static $STATUS_AWAITING_PROXY = 'AWAITING_PROXY';
	/**
	 * The appeal in question has been resolved
	 */
	public static $STATUS_CLOSED = 'CLOSED';
	
	/**
	 * Database ID number
	 */
	private $idNum;
	/**
	 * The IP address used to make the request; presumably the blocked one
	 * if the appealer doesn't have an account or it's an auto or rangeblock.
	 */
	private $ipAddress;
	/**
	 * The appealer's email address
	 */
	private $emailAddress;
	/**
	 * If the user already has an account
	 */
	private $hasAccount;
	/**
	 * The user's existing or desired account name
	 */
	private $accountName;
	/**
	 * If this is an auto- or range-block
	 */
	private $isAutoblock;
	/**
	 * The blocking administrator
	 */
	private $blockingAdmin;
	/**
	 * The text of the appeal
	 */
	private $appeal;
	/**
	 * What edits the user intends to make if unblocked
	 */
	private $intendedEdits;
	/**
	 * Other information
	 */
	private $otherInfo;
	/**
	 * Time the request was placed
	 */
	private $timestamp;
	/**
	 * The admin handling the appeal
	 */
	private $handlingAdmin;
	/**
	 * Status of the appeal
	 */
	private $status;
	
	public function __construct(array $postVars){
		validate($postVars); // may throw an exception
		
		$this->ipAddress = $_SERVER['REMOTE_ADDR'];
		$this->emailAddress = $postVars['email'];
		$this->hasAccount = (boolean) $postVars['registered'];
		$this->accountName = $postVars['accountName'];
		$this->isAutoBlock = (boolean) $postVars['autoBlock'];
		$this->blockingAdmin = $postVars['blockingAdmin'];
		$this->appeal = $postVars['appeal'];
		$this->intendedEdits = $postVars['intendedEdits'];
		$this->otherInfo = $postVars['otherInfo'];
		$this->timestamp = date('Y-m-d H:i:s');
		$this->handlingAdmin = null;
		$this->status = $STATUS_NEW;
		
		// TODO: insert into database
		
		// TODO: get database's assigned ID number
	}
	
	public static function validate(array $postVars){
		$errorMsgs = "";
		$hasAccount = false;
		
		echo 'mark 1';
		
		// confirm that all required fields exist
		if(!isset($postVars["email"])){
			$errorMsgs .= "<br />An email address is required in order to stay in touch with you about your appeal.";
		}
		echo 'mark 2';
		if(!isset($postVars["registered"])){
			$errorMsgs .= "<br />We need to know if you have an account on the English Wikipedia.";
		}
		else{
			$hasAccount = $postVars["registered"];
		}
		echo 'mark 3';
		if($hasAccount && !isset($postVars["accountName"])){
			$errorMsgs .= "<br />If you have an account, we need to know the name of your account.";
		}
		echo 'mark 4';
		if($hasAccount && !isset($postVars["autoBlock"])){
			$errorMsgs .= "<br />If you have an account, we need to know if you are appealing a direct block or an IP block.";
		}
		echo 'mark 5';
		if(!isset($postVars["blockingAdmin"])){
			$errorMsgs = $errorMsgs . "<br />We need to know which administrator placed your block.";
		}
		echo 'mark 6';
		if(!isset($postVars["appeal"])){
			$errorMsgs .= "<br />You have not provided a reason why you wish to be unblocked.";
		}
		echo 'mark 7';
		if(!isset($postVars["edits"])){
			$errorMsgs .= "<br />You have not told us what edits you wish to make once unblocked.";
		}
		echo 'mark 9';
		
		// validate fields
		if(isset($postVars["email"])){
			$email = $postVars["email"];
			if(preg_match("[A-Za-z0-9_-]*@[A-Za-z0-9_-]*\.[a-z]{2,3}(\.[a-z]{2,3})?", $email) != 1){
				$errorMsgs .= "<br />An email address is required in order to stay in touch with you about your appeal.";
			}
		}
		
		echo 'mark 10';
		
		// TODO: add queries to check if account exists or not
		
		if($errorMsgs){ // empty string is falsy
			echo 'mark 10.5';
			throw new UTRSValidationException($errorMsgs);
		}
		echo 'mark 11';
	}
	
	public function getID(){
		return $this->idNum;
	}
	
	public function getIP(){
		return $this->ipAddress;
	}
	
	public function getEmail(){
		return $this->emailAddress;
	}
	
	public function hasAccount(){
		return $this->hasAccount;
	}
	
	public function getAccountName(){
		return $this->accountName;
	}
	
	public function isAutoblock(){
		return $this->isAutoblock;
	}
	
	public function getBlockingAdmin(){
		return $this->blockingAdmin;
	}
	
	public function getAppeal(){
		return $this->appeal;
	}
	
	public function getIntendedEdits(){
		return $this->intendedEdits;
	}
	
	public function getOtherInfo(){
		return $this->otherInfo;
	}
	
	public function getTimestamp(){
		return $this->timestamp;
	}
	
	public function getHandlingAdmin(){
		return $this->handingAdmin;
	}
	
	public function getStatus(){
		return $this->status;
	}
	
	public function setStatus($newStatus){
		// TODO: query to check if status is closed; if so, whoever's reopening
		// should be a tool admin
		if(strcmp($newStatus, $STATUS_NEW) == 0 || strcmp($newStatus, $STATUS_AWAITING_USER) == 0
		  || strcmp($newStatus, $STATUS_AWAITING_ADMIN) == 0 || strcmp($newStatus, $STATUS_AWAITING_CHECKUSER) == 0
		  || strcmp($newStatus, $STATUS_AWAITING_PROXY) == 0 || strcmp($newStatus, $STATUS_CLOSED) == 0){
			// TODO: query to modify the row
			$this->status = $newStatus;
		}
		else{
			// Note: this shouldn't happen
			throw new UTRSIllegalModificationException("The status you provided is invalid.");
		}
	}
	
	public function setHandlingAdmin($admin){
		if($this->handlingAdmin != null){
			throw new UTRSIllegalModificationException("This request is already reserved. "
			  . "If the person holding this ticket seems to be unavailable, ask a tool "
			  . "admin to break their reservation.");
		}
		// TODO: Add a check to ensure that each person is only handling one 
		// at a time? Or allow multiple reservations?
		
		// TODO: query to modify the row
		
		$this->handlingAdmin = $admin;
	}
}

?>