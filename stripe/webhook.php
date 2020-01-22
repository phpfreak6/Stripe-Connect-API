<?php   
error_reporting(-1);   
session_start();

require_once('../config.php');  
require_once('../includes/functions.php');
require_once('init.php');   
  
$noti_text = array(
				"external_account" => "Please provide bank account information to your account profile. Click Here" ,
				"legal_entity.additional_owners" => "Please provide information about additional owners of your company Click Here" , 
				"legal_entity.address.city" => "Please provide city address information. Click Here" , 
				"legal_entity.address.line1" => "Please provide the 1st line of your address. Click Here" , 
				"legal_entity.address.postal_code" => "Please provide the post/zip code of your address. Click Here" , 
				"legal_entity.business_name" => "Please provide your Business name. Click Here" , 
				"legal_entity.business_tax_id" => "Please provide your business tax/vat id number.  Click Here" , 
				"legal_entity.dob.day" => "Please enter the day of your date of birth. Click Here" , 
				"legal_entity.dob.month" => "Please enter the month of your date of birth.  Click Here" , 
				"legal_entity.dob.year" => "Please enter the year of your date of birth. Click Here" , 
				"legal_entity.first_name" => "Please enter your first name in your account details.  Click Here" , 
				"legal_entity.last_name" => "Please enter your last name in your account details. Click Here" , 
				"legal_entity.personal_address.city" => "Please enter the city of your personal address. Click Here" , 
				"legal_entity.personal_address.line1" => "Please enter line 1 of your personal address.  Click Here" , 
				"legal_entity.personal_address.postal_code" => "Please enter the postal/zip code of your personal address.Click Here" , 
				"legal_entity.type" => "Please enter the type of business you are running. Click Here" , 
				"tos_acceptance.date" => "Please accept our updated terms and conditions. Click Here" , 
				"tos_acceptance.ip" => "IP Address recoding is invalid please contact Breeze Support." , 
				"legal_entity.verification.document" => "Please upload your photo page of your passport. Click Here" , 
				"legal_entity.address.state" => "Please provide us with your state address details.  Click Here" , 
				"legal_entity.ssn_last_4" => "Please provide us with the last 4 digits of your social security number.  Click Here" , 
				"legal_entity.personal_id_number" => "Please provide us with your personal ID number.  Click Here" 
			);

// retrieve the request's body and parse it as JSON  
$body = @file_get_contents('php://input');   
$res = json_decode($body);   
print_r($res); 
$event_id 			= $res->id;  
$user_id = $res->data->object->id;
// $user_id = 'acct_18oYB4BMgRxhae6p';
$sql = "SELECT * FROM `availcal_users` WHERE stripemangedid='$user_id' LIMIT 1";
$rs  = mysql_query($sql, $con2)or die(mysql_error($con2));
$userifo = mysql_fetch_assoc($rs);
$stripesecretkey     =  $userifo['stripesecretkey'];

\Stripe\Stripe::setApiKey($stripesecretkey);  

$object 			= $res->object;
$api_version 		= $res->api_version;
$created 			= date("Y-m-d H:i:s", $res->created);
$data 				= json_encode($res->data);
$livemode 			= $res->livemode;
$pending_webhooks 	= $res->pending_webhooks;
$request 			= $res->request;
$type 				= $res->type;

$sql = "INSERT INTO `B_TSRIPEEVENTS` SET OBJECT = '$object', API_VERSION = '$api_version', CREATED = '$created', DATA = '$data', LIVEMODE = '$livemode', PENDING_WEBHOOKS = '$pending_webhooks', REQUEST = '$request', TYPE = '$type'";
mysql_query($sql, $con2);


$user_id 			= $userifo['id'];  
$fields_needed 		= $res->data->object->verification->fields_needed;
$due_by 			= $res->data->object->verification->due_by;
$disabled_reason	= $res->data->object->verification->disabled_reason;
$status		  		= $res->data->object->legal_entity->verification->status;
$notification_text  = json_encode($ntext);
$db_needed = array();
if($pending_webhooks > 0){
	
	$sql = "SELECT * FROM `T_BSTRIPEACCTVERIFY` WHERE User_ID = '$user_id'";
	$rss  = mysql_query($sql, $con2)or die(mysql_error($con2));
	while($useract = mysql_fetch_assoc($rss)){
		
		$id = $useract['ID'];
		$db_needed[] = $useract['Field_Needed'];
		if(!in_array($useract['Field_Needed'],$fields_needed)){
			$sql = "UPDATE `T_BSTRIPEACCTVERIFY` SET Status = 'resolved' WHERE ID = '$id'";
			mysql_query($sql, $con2);
		}		
	}
	
	$ntext = array();
	
	foreach($fields_needed as $fieldn){
		
		$ntext = $noti_text[$fieldn];
		// if($fieldn != 'legal_entity.dob.day' && $fieldn != 'legal_entity.dob.month' && $fieldn != 'legal_entity.dob.year' && $fieldn != 'legal_entity.first_name' && $fieldn != 'legal_entity.last_name'){
			//$sql = "INSERT INTO `T_BSTRIPEACCTVERIFY` SET Event_ID = '$event_id', Field_Needed = '$fieldn', Due_By = '$due_by', Disabled_Reason = '$disabled_reason', User_ID = '$user_id', Notification_Text = '$ntext', Status = '$status'";
				if(in_array($fieldn,$db_needed)){
					
					$sql = "UPDATE `T_BSTRIPEACCTVERIFY` SET Event_ID = '$event_id', Due_By = '$due_by', Disabled_Reason = '$disabled_reason', Notification_Text = '$ntext', Status = '$status' WHERE User_ID = '$user_id' AND Field_Needed LIKE '$fieldn'";
				
					
				}else{
					
					$sql = "INSERT INTO `T_BSTRIPEACCTVERIFY` SET Event_ID = '$event_id', Field_Needed = '$fieldn', Due_By = '$due_by', Disabled_Reason = '$disabled_reason', User_ID = '$user_id', Notification_Text = '$ntext', Status = '$status'";
					
					
				}
				mysql_query($sql, $con2);
			}
		
}
else
{
	$sql = "UPDATE `T_BSTRIPEACCTVERIFY` SET Status = 'resolved' WHERE User_ID = '$user_id'";
	mysql_query($sql, $con2);
}
?>