<?php
$submit_form		= get_param($_POST, 'submit_form', 0);
// $firstname          = get_param($userinfo,	'first_name');
// $lastname      		= get_param($userinfo,	'last_name');
// $date     			= get_param($userinfo,	'date');
// $month				= get_param($userinfo,	'month');
// $year				= get_param($userinfo,	'year');
$address1			= get_param($userinfo,	'address1');
$address2			= get_param($userinfo,	'address2');
$city				= get_param($userinfo,	'city');
$postal_code	    = get_param($userinfo,	'postal_code');

$paddress1			= get_param($userinfo,	'paddress1');
$paddress2			= get_param($userinfo,	'paddress2');
$pcity				= get_param($userinfo,	'pcity');
$ppostal_code	    = get_param($userinfo,	'ppostal_code');

$bank_name          = get_param($userinfo,	'bank_name');
$account_number     = get_param($userinfo,	'account_number');
$routing_number     = get_param($userinfo,	'routing_number');
$add_check 			= get_param($userinfo,	'add_check');
$business_name      = get_param($userinfo,	'business_name');
$business_tax_id    = get_param($userinfo,	'business_tax_id');


if($add_check == 'no'){
	$addown 	   = $userinfo['addown'];		
}
 
 
$sql1 = "SELECT * from T_BSTRIPEACCTVERIFY where User_ID='".$user_id."' && Status = 'unverified'";  
$rssss = mysql_query($sql1, $con2) or die($sql1.mysql_error($con2));   
$p = array();
$needed_fields = array();
if(mysql_num_rows($rssss)){
	while($rows=mysql_fetch_object($rssss)){
		$needed_fields[] = $rows->Field_Needed;
		$val = explode(".",$rows->Field_Needed);
		$p[] = end($val);
	}
}	

$doc = false;
if(in_array('document',$p)){
	$doc = true;
}




$show_error = array();
if(!empty($submit_form))
{
	
	$firstname1         = get_param($_POST,	'first_name');
	$lastname1          = get_param($_POST,	'last_name');
	$date1     			= get_param($_POST,	'date');
	$month1				= get_param($_POST,	'month');
	$year1				= get_param($_POST,	'year');
	$address1			= get_param($_POST,	'address1');
	$address2			= get_param($_POST,	'address2');
	$city				= get_param($_POST,	'city');
	$postal_code	    = get_param($_POST,	'postal_code');

	$paddress1			= get_param($_POST,	'paddress1');
	$paddress2			= get_param($_POST,	'paddress2');
	$pcity				= get_param($_POST,	'pcity');
	$ppostal_code	    = get_param($_POST,	'ppostal_code');

	$bank_name          = get_param($_POST,	'bank_name');
	$account_number     = get_param($_POST,	'account_number');
	$routing_number     = get_param($_POST,	'routing_number');
	$add_check 			= get_param($_POST,	'add_check');

	$business_name      = get_param($_POST,	'business_name');
	$business_tax_id    = get_param($_POST,	'business_tax_id');
	
	if($add_check == 'no'){
		$addown 	   = $_POST['addown'];
		
	}
	
	
	if(empty($stripemangedid)){
		try{			
		$acct = \Stripe\Account::create(array(
					"managed" => true,
					"country" => $scountry,
					"external_account" => array(
					"object" => "bank_account",
					"country" => $scountry,
					"currency" => $scurrency,
					"routing_number" => $routing_number,
					"account_number" => $account_number,
				),
					"tos_acceptance" => array(
					"date" => $stimestamp,
					"ip" => $sip
				)
		)); 
		}catch(Exception $e) {
			echo "<pre>";
			print_r($e);
			echo "</pre>";
			if($e->jsonBody['error']['message'] == "You cannot use a live bank account number when making transfers or debits in test mode"){
				 $e->jsonBody['error']['param'] = 'account_number';
			}

			switch ($e->jsonBody['error']['param']) {
				case "account_number":
					$show_error['account_number'] = $e->jsonBody['error']['message'];
					break;
				case "external_account":
					if($e->jsonBody['error']['message'] == 'Must have at least one letter'){
						$show_error['routing_number'] = "You Must enter the Account Number and Routing Code.";
					}else
					{
						$show_error['routing_number'] = $e->jsonBody['error']['message'];
					}
					break;
				default:
					$show_error['stripe_error'] = $e->jsonBody['error']['message'];
					break;

			}
		}
		$secret = $acct->keys->secret;
		$publishable = $acct->keys->publishable;
		$sql = "UPDATE `availcal_users` SET stripemangedid='$acct->id' , stripesecretkey='$secret' , stripepublishablekey='$publishable'  where id='$user_id'";
		mysql_query($sql, $con2);
		$stripemangedid = $acct->id;
		
		
		$account = \Stripe\Account::retrieve($stripemangedid);

			if(!empty($firstname1)){ $account->legal_entity->first_name = $firstname1; }

			if(!empty($lastname1)){ $account->legal_entity->last_name = $lastname1; }

			if(!empty($date1)){ $account->legal_entity->dob->day = $date1; }

			if(!empty($month1)){ $account->legal_entity->dob->month = $month1; }

			if(!empty($year1)){ $account->legal_entity->dob->year = $year1; }
			
		$account->save();
		
	}
	
	
	
	try{
		$account = \Stripe\Account::retrieve($stripemangedid);
	
		
		
		
			
				
				if(in_array('first_name',$p)){
					if(!empty($firstname1)){ $account->legal_entity->first_name = $firstname1; }
				}
				if(in_array('last_name',$p)){
					if(!empty($lastname1)){ $account->legal_entity->last_name = $lastname1; }
				}
				if(in_array('day',$p)){
					if(!empty($date1)){ $account->legal_entity->dob->day = $date1; }
				}
				if(in_array('month',$p)){
					if(!empty($month1)){ $account->legal_entity->dob->month = $month1; }
				}
				if(in_array('year',$p)){
					if(!empty($year1)){ $account->legal_entity->dob->year = $year1; }
				}
				
		
		
		
		
		
		if(!empty($business_name)){  $account->legal_entity->business_name = $business_name; }
		if(!empty($accounttype)){  $account->legal_entity->type = $accounttype; }
		
		if(!empty($city)){  $account->legal_entity->address->city = $city; }
		if(!empty($address1)){  $account->legal_entity->address->line1 = $address1; }
		if(!empty($postal_code)){  $account->legal_entity->address->postal_code = $postal_code; }		
		if(!empty($address2)){  $account->address->line2 = $address2; }
		
		
		if(!empty($business_tax_id)){  $account->legal_entity->business_tax_id = $business_tax_id; }
		if(!empty($pcity)){  $account->legal_entity->personal_address->city = $pcity; }
		if(!empty($paddress1)){  $account->legal_entity->personal_address->line1 = $paddress1; }
		if(!empty($ppostal_code)){  $account->legal_entity->personal_address->postal_code = $ppostal_code;	 }	
		if(!empty($address2)){  $account->personal_address->line2 = $address2; }
		
		if(!empty($routing_number) && !empty($account_number) ){
					
					$account->external_account = array(
								"object" => "bank_account",
								"country" => $scountry,
								"currency" => $scurrency,
								"routing_number" => $routing_number,
								"account_number" => $account_number,
							);
					
					
				}
		$account->save();
	}
	catch(Exception $e) {
		
		if($e->jsonBody['error']['message'] == "You cannot use a live bank account number when making transfers or debits in test mode"){
			$e->jsonBody['error']['param'] = 'account_number';
		}

		
		switch ($e->jsonBody['error']['param']) {
			case "account_number":
				$show_error['account_number'] = $e->jsonBody['error']['message'];
				break;
			case "external_account":
				$show_error['routing_number'] = $e->jsonBody['error']['message'];
				break;
			default:
				$show_error['stripe_error'] = $e->jsonBody['error']['message'];
				break;

		}
		
	}	
	

	$nrr = array();
	for($on = 0; $on<=3; $on++){
		$account = \Stripe\Account::retrieve($stripemangedid);
		$file_obj = \Stripe\FileUpload::create(
			array(
				"purpose" => "identity_document",
				"file" => fopen('stripe/stripe.png', 'r')
			),
			array(
				"stripe_account" => $stripemangedid
			)
		);
		$file = $file_obj->id;
		if(!empty($_POST['addown'][$on]['first_name'])){
			$nrrs = array();
			// $nrr[] = $_POST['addown'][$on];
			$nrrs['verification']['document'] = $file;
			
			$nrr[]  = array_merge($_POST['addown'][$on],$nrrs);
		}
		
	}
	
	
	// $nrr = arrayToObject($nrr);
	// $nrr = (array)$nrr;
// echo "<pre>";
// print_r($nrr);
	if($add_check == 'no'){
		$_POST['addown'] = $nrr;
		try{
		$account = \Stripe\Account::retrieve($stripemangedid);
		$account->legal_entity->additional_owners = $_POST['addown'];
		$account->save();
		
		}
	catch(Exception $e) {
		
		// echo "<pre>";
// print_r($e);
	}
		
	} 
	
			
		
	$sqld = "SELECT * FROM `availcal_users_details` WHERE user_id='$user_id' LIMIT 1";
	$rsd  = mysql_query($sqld, $con2)or die(mysql_error($con2));
	$numd = mysql_num_rows($rsd);
	$userifod = mysql_fetch_assoc($rsd);
	if($numd > 0){
		$sql1 = "UPDATE `availcal_users_details` SET data='".json_encode($_POST)."' where user_id='$user_id'";
		mysql_query($sql1, $con2);

		$diff = array_diff($_POST, $userinfo);
	
		foreach($diff as $info => $in){
			$ts = date("Y-m-d H:i:s");
			$oldvl = $userinfo[$info];
			$sql1 = "INSERT INTO `B_TACCOUNTTRACK` SET user_id='$user_id', Timestamp='".$ts."' , Field_Name='$info', Original_Data='$oldvl', New_Data='$in' ";
			mysql_query($sql1, $con2);
			
		}
		
	}else
	{
		$sql1 = "INSERT INTO `availcal_users_details` SET data='".json_encode($_POST)."' , user_id='$user_id'";
		mysql_query($sql1, $con2);
	}
		

	if(!empty($_FILES["document"]["name"])){
		
		$target_dir = "stripe/documents/";
		$target_file = $target_dir . basename($user_id.'-'.$_FILES["document"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		move_uploaded_file($_FILES["document"]["tmp_name"], $target_file);

		$account = \Stripe\Account::retrieve($stripemangedid);
		$file_obj = \Stripe\FileUpload::create(
			array(
				"purpose" => "identity_document",
				"file" => fopen($target_file, 'r')
			),
			array(
				"stripe_account" => $stripemangedid
			)
		);
		$file = $file_obj->id;
		$account->legal_entity->verification->document = $file;
		$account->save();
		
		$sql = "UPDATE `availcal_users_details` SET file='".json_encode($_FILES)."' where user_id='$user_id'";
		mysql_query($sql, $con2);
		
		$sql = "UPDATE `T_BSTRIPEACCTVERIFY` SET Status = 'resolved' WHERE User_ID = '$user_id' and Field_Needed = 'legal_entity.verification.document'";
				mysql_query($sql, $con2);
		
	}
		
	if(in_array( "legal_entity.verification.document" , $account->verification->fields_needed)){
		$show_error['document'] = 'Please upload the verification document.';
		$doc = true;
	}
	
		$account = \Stripe\Account::retrieve($stripemangedid);
				
				 $user_id 			= $user_id;  
				 $fields_needed 	= $account->verification->fields_needed;
				 $due_by 			= $account->verification->due_by;
				 $disabled_reason	= $account->verification->disabled_reason;
				 $status		  	= $account->legal_entity->verification->status;
				 $event_id = '';
				 	
					$sql = "UPDATE `T_BSTRIPEACCTVERIFY` SET Status = 'resolved' WHERE User_ID = '$user_id'";
					
					mysql_query($sql, $con2);
					
				 foreach($fields_needed as $fieldn){
						
						$ntext = $noti_text[$fieldn];
						// if($fieldn != 'legal_entity.dob.day' && $fieldn != 'legal_entity.dob.month' && $fieldn != 'legal_entity.dob.year' && $fieldn != 'legal_entity.first_name' && $fieldn != 'legal_entity.last_name'){
							
							if(in_array($fieldn,$needed_fields)){
								
							$sql = "UPDATE `T_BSTRIPEACCTVERIFY` SET Due_By = '$due_by', Disabled_Reason = '$disabled_reason',Notification_Text = '$ntext', Status = '$status' where  Field_Needed = '$fieldn' AND User_ID = '$user_id'";
							
							mysql_query($sql, $con2);
							
							}else{
								
							$sql = "INSERT INTO `T_BSTRIPEACCTVERIFY` SET Event_ID = '$event_id', Field_Needed = '$fieldn', Due_By = '$due_by', Disabled_Reason = '$disabled_reason', User_ID = '$user_id', Notification_Text = '$ntext', Status = '$status'";
								
								
							}
						// }
						
					}
} 

?>