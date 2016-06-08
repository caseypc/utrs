<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('session.use_cookies', '1');

require_once('template.php');
require_once('src/messages.php');
require_once('src/exceptions.php');

$destination = getRootURL() . 'home.php';

if(isset($_COOKIE["language"]) && !isset($_GET["reset"])){
	header("Location: " . $destination);
}
else {
	if(isset($_GET['set'])) {
		setcookie("language",$_GET['set'],time()+31557600);
	}
	else {
//Template header()
skinHeader();
try {
	throw new UTRSNetworkException(
			SystemMessages::$error['LangError']['en']." ".
			SystemMessages::$error['LangError']['pt']
			);
} catch (UTRSNetworkException $ex){
   	  $errorMessages = $ex->getMessage();
   	  displayError($errorMessages);
}

?>
<center>
<b><?php 
echo SystemMessages::$system['SelectLang']['en'];
echo SystemMessages::$system['SelectLang']['pt'];
?></b>
<br><br><br><br>
<img src="https://upload.wikimedia.org/wikipedia/en/thumb/a/ae/Flag_of_the_United_Kingdom.svg/40px-Flag_of_the_United_Kingdom.svg.png"> <a href = "langVerify.php?set=en">English Wikipedia (en.wikipedia.org)</a>
<br><br><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Flag_of_Portugal.svg/40px-Flag_of_Portugal.svg.png"> <a href = "langVerify.php?set=pt">Wikip�dia portuguesa (pt.wikipedia.org)</a>
</center>
<?php 

skinFooter();
	}
}

?>