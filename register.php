<?php
session_start();
require_once('config.php');
require_once('stripe/n/init.php');
require_once('includes/functions.php');
function getUniqueCode($length){	
for($i=0;$i<$length;$i++){		

if($i==0)			
	
	$code = str_pad(mt_rand(1, 9), 1, '0', STR_PAD_LEFT);		
	
else			
	
	$code .=   str_pad(mt_rand(0, 9), 1, '0', STR_PAD_LEFT);	
	
}	

return $code;

} 
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
$submit_form		= get_param($_POST, 'submit_form', 0);
$firstname			= get_param($_POST,	'firstname');
$lastname			= get_param($_POST,	'lastname');
$username			= get_param($_POST,	'username');$date				= get_param($_POST,	'date');$month				= get_param($_POST,	'month');$year				= get_param($_POST,	'year');
$country            = get_param($_POST,	'country');
$business_type      = get_param($_POST,	'business_type');
$password			= get_param($_POST,	'password');
$confirm_password	= get_param($_POST,	'confirm_password');
$terms				= get_param($_POST,	'terms');
$breezeaccountid      = getUniqueCode(16); 
$stripeaccounttype    = $business_type;
$tos_acceptancedate   = date("Y-m-d H:i:s");
$tos_acceptanceip     = $_SERVER['REMOTE_ADDR'];
switch ($country) {	

case "GB":		

$currency = 'GBP';	

break;	

case "US":	
	
$currency = 'USD';	

break;	
default:

$currency = 'USD';	
break;

} 

if($terms != '')
	
	$terms = 1;
else
	
	$terms = 0;
	

$show_error = array();

if($submit_form)
{
	if($firstname=='')
	{
		$show_error['firstname'] = 'Please enter Firstname';
	}
	if($lastname=='')
	{
		$show_error['lastname'] = 'Please enter Lastname';
	}
	if($username=='')
	{
		$show_error['username'] = 'Please enter Username';
	}
	elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) 
	{
	  $show_error['username'] = 'Invalid email format';
	}
	if($country=='')
	{
		$show_error['country'] = 'Please enter Mobile';
	}		
	if( $country!='US' && $country!='GB' )	{				
	$show_error['country'] = 'Sorry we are unable to register accounts in your country at this time';			
	}
	if($business_type=='')
	{
		$show_error['business_type'] = 'Please select Business Type';
	}
	if($password=='')
	{
	  $show_error['password'] = 'Please enter Password';
	}
	if($confirm_password=='')
	{
	  $show_error['confirm_password'] = 'Please Re-enter Password';
	}
	
	if($password!= '' && $password!=$confirm_password)
	{
	  $show_error['password'] = 'Password miss match';
	}
	if(!$terms)
	{
		$show_error['terms'] = 'Please check terms';
	}
	if(empty($show_error))
	{
		$sql = "SELECT * FROM `availcal_users` WHERE email='$username' LIMIT 1";
		$rs  = mysql_query($sql, $con2)or die(mysql_error($con2));
		$num = mysql_num_rows($rs);
		if($num>0){
			$show_error['username'] = 'Email already exists';
		}
	}
	
	if(empty($show_error))
	{		$dob = $date.'-'.$month.'-'.$year;
		//$sql = "INSERT INTO `availcal_users` SET firstname='$firstname', lastname='$lastname',  country='$country', email='$username', password='$password', status='active', permission='technician', register='1', breezeaccountid = '$breezeaccountid', stripeaccounttype = '$stripeaccounttype', tos_acceptancedate = '$tos_acceptancedate', tos_acceptanceip = '$tos_acceptanceip', currency = '$currency'";
		$sql = "INSERT INTO `availcal_users` SET firstname='$firstname', lastname='$lastname',  country='$country', email='$username', password='$password', status='active', permission='technician', register='1', breezeaccountid = '$breezeaccountid', stripeaccounttype = '$stripeaccounttype', tos_acceptancedate = '$tos_acceptancedate', tos_acceptanceip = '$tos_acceptanceip', currency = '$currency', sceoname='$firstname', dob='$dob'";
		if(mysql_query($sql, $con2)){
			$id = mysql_insert_id($con2);
			
			
			
			$sql = "SELECT * FROM `availcal_users` WHERE email='$username' AND password='".mysql_real_escape_string($password)."'";
			
			$rs  = mysql_query($sql, $con2)or die(mysql_error($con2));
			
			$num = mysql_num_rows($rs);

			if($num>0)

			{

				$row = mysql_fetch_object($rs);

				

				$_SESSION['user_id'] = $row->id;

				$_SESSION['email'] = $row->email;

				$_SESSION['is_admin'] = $row->permissions == 'admin'? 1:0;
				
				
			\Stripe\Stripe::setApiKey('Pass secret key here');	
			
			try{		
			
				$sdate = new DateTime();
				
				$stimestamp =  $sdate->getTimestamp();
				
				$sip =   $_SERVER['REMOTE_ADDR'];
				
				
				$acct = \Stripe\Account::create(array(
				
					"managed" => true,
					
					"country" => $country,
					
				)); 
				
				$stripemangedid = $acct->id;
				$user_id = $row->id;
				$secret = $acct->keys->secret;
				$publishable = $acct->keys->publishable;
				$sql = "UPDATE `availcal_users` SET stripemangedid='$acct->id' , stripesecretkey='$secret' , stripepublishablekey='$publishable'  where id='$user_id'";
				mysql_query($sql, $con2);
				$stripemangedid = $acct->id;
				
				$account = \Stripe\Account::retrieve($stripemangedid);

					if(!empty($firstname)){ $account->legal_entity->first_name = $firstname; }

					if(!empty($lastname)){ $account->legal_entity->last_name = $lastname; }

					if(!empty($date)){ $account->legal_entity->dob->day = $date; }

					if(!empty($month)){ $account->legal_entity->dob->month = $month; }

					if(!empty($year)){ $account->legal_entity->dob->year = $year; }
					if(!empty($stripeaccounttype)){ $account->legal_entity->type = $stripeaccounttype; }
					$account->tos_acceptance->date = $stimestamp;
					
					$account->tos_acceptance->ip = $sip;
					
				$account->save();
			
				$account = \Stripe\Account::retrieve($stripemangedid);
				
				/*   echo "<pre>";
					print_r($account_details);
					print_r($account_details['verification']);
					
				 echo "</pre>";
			     die('here'); */
				 
				 $user_id 			= $user_id;  
				 $fields_needed 	= $account->verification->fields_needed;
				 $due_by 			= $account->verification->due_by;
				 $disabled_reason	= $account->verification->disabled_reason;
				 $status		  	= $account->legal_entity->verification->status;
				 $event_id = '';
				 
				 foreach($fields_needed as $fieldn){
					 
						$ntext = $noti_text[$fieldn];
						// if($fieldn != 'legal_entity.dob.day' && $fieldn != 'legal_entity.dob.month' && $fieldn != 'legal_entity.dob.year' && $fieldn != 'legal_entity.first_name' && $fieldn != 'legal_entity.last_name'){
							$sql = "INSERT INTO `T_BSTRIPEACCTVERIFY` SET Event_ID = '$event_id', Field_Needed = '$fieldn', Due_By = '$due_by', Disabled_Reason = '$disabled_reason', User_ID = '$user_id', Notification_Text = '$ntext', Status = '$status'";
							mysql_query($sql, $con2);
						// }
						
					}
				
			}catch(Exception $e) {
				
				echo "<pre>";
				print_r($e);
				echo "</pre>";
			}
			}
			header('Location:account.php');
			// header('Location:login.php');
			exit;
		}
		else{
			echo mysql_error($con2);
		} 
	}
}
mysql_close($con2);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Kode is a Premium Bootstrap Admin Template, It's responsive, clean coded and mobile friendly">
  <meta name="keywords" content="bootstrap, admin, dashboard, flat admin template, responsive," />
  <title>BREEZE REGISTER</title>

  <!-- ========== Css Files ========== -->
  <link href="css/root.css" rel="stylesheet">
  <link href="css/custom.css" rel="stylesheet">
  <style type="text/css">
    body{background: #F5F5F5;}
	.login-form .footer-links a:link, .login-form .footer-links a:hover, .login-form .footer-links a:visited{
		color:#399bff;
		font-size:14px;
	}
	.footer-links{ /*margin-bottom:50px;*/}
	.login-form{ width:300px; padding-top:10px;}
	.login-form form .top .icon{ height:70px; width:auto;} 
	.login-form form img{ margin-bottom:0px;}
	.login-form form .form-area{ padding:20px;}
	.login-form form .top{ padding:20px 0;}
	@import url('http://getbootstrap.com/dist/css/bootstrap.css');
	 html, body, .container-table {
		height: 100%;
	}
	.container-table {
		display: table;
	}
	.vertical-center-row {
		display: table-cell;
		vertical-align: middle;
	}
  </style>
  </head>
  <body>
    <div class="container container-table">
        <div class="login-form row vertical-center-row">
          <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <div class="top">
              <img src="img/logo_swish.png" alt="icon" class="icon">
              <?php if(count($show_error)):?>
                <?php echo '<h4>'.implode('</h4><h4>', $show_error).'</h4>';?>
              <?php endif;?>
            </div>
            <div class="form-area">
              <div class="group">
                <input type="text" name="firstname" class="form-control<?php if(isset($show_error['firstname']))echo ' show-error';?>" value="<?php echo $firstname;?>" placeholder="First Name">
                <i class="fa fa-list"></i>
              </div>
              <div class="group">
                <input type="text" name="lastname" class="form-control<?php if(isset($show_error['lastname']))echo ' show-error';?>" value="<?php echo $lastname;?>" placeholder="Last Name">
                <i class="fa fa-list"></i>
              </div>
              <div class="group">
                <input type="text" name="username" class="form-control<?php if(isset($show_error['username']))echo ' show-error';?>" value="<?php echo $username;?>" placeholder="E-mail">
                <i class="fa fa-envelope-o"></i>
              </div>
              <div class="group">				<select name="country" class="form-control<?php if(isset($show_error['country']))echo ' show-error';?>">				<option value="">Country </option>				<?php				$countries = array("US"=>"United States","AU"=>"Australia" ,"AT"=>"Austria" ,"BE"=>"Belgium" ,"CA"=>"Canada" ,"DK"=>"Denmark" ,"FI"=>"Finland" ,"FR"=>"France" ,"DE"=>"Germany" ,"HK"=>"Hong Kong" ,"IE"=>"Ireland" ,"IT"=>"Italy" ,"JP"=>"Japan" ,"LU"=>"Luxembourg" ,"NL"=>"Netherlands" ,"NO"=>"Norway" ,"PT"=>"Portugal" ,"SG"=>"Singapore" ,"ES"=>"Spain" ,"SE"=>"Sweden" ,"GB"=>"United Kingdom" );										foreach($countries as $k => $v){										if($country == $k){												echo '<option selected value="'.$k.'">'.$v.'</option>';											}else											{												echo '<option value="'.$k.'">'.$v.'</option>';											}									}								?>							</select>
            <i class="fa fa-list"></i>
              </div>
              <div class="group">
                <select name="business_type" class="form-control<?php if(isset($show_error['business_type']))echo ' show-error';?>">					<option value="">Business Type </option>										<?php					$types = array("corporation"=>"Corporation" ,"sole_prop"=>"Individual / Sole Proprietorship" ,"non_profit"=>"Non-profit" ,"partnership"=>"Partnership" ,"llc"=>"LLC");											foreach($types as $k => $v){												if($business_type == $k){														echo '<option selected value="'.$k.'">'.$v.'</option>';													}else													{							echo '<option value="'.$k.'">'.$v.'</option>';													}											}									?>				</select>
                <i class="fa fa-list"></i>
              </div>
              <div class="group"><div class="row">			<div class="col-md-4">
                <select name="date" class="form-control<?php if(isset($show_error['date']))echo ' show-error';?>" style="padding:0">					<option value="">-Day-</option>					<?php					for($i=1; $i<=31; $i++){						if($date == $i){							echo '<option selected value="'.$i.'">'.$i.'</option>';						}else						{							echo '<option value="'.$i.'">'.$i.'</option>';						}										}					?>				</select>				</div>				<div class="col-md-4">				<select name="month" class="form-control<?php if(isset($show_error['month']))echo ' show-error';?>" style="padding:0">					<option value="">-Month-</option>					<?php					for ($m=1; $m<=12; $m++) {						$months = date('F', mktime(0,0,0,$m, 1, date('Y')));										 						if($month == $m){							echo '<option  selected  value="'.$m.'">'.$months.'</option>';						}else						{							echo '<option  value="'.$m.'">'.$months.'</option>';						}						}					?>				</select>				</div>				<div class="col-md-4">				<select name="year" class="form-control<?php if(isset($show_error['year']))echo ' show-error';?>" style="padding:0">					<option value="">-Year-</option>					<?php					for($i=date("Y"); $i>=1960; $i--){						if($year == $i){							echo '<option selected value="'.$i.'">'.$i.'</option>';						}else						{							echo '<option value="'.$i.'">'.$i.'</option>';						}					}					?>				</select></div></div>
              </div>			  			  <div class="group">
                <input type="password" name="password" class="form-control<?php if(isset($show_error['password']))echo ' show-error';?>" value="<?php echo $password;?>"  placeholder="Password">
                <i class="fa fa-key"></i>
              </div>
              <div class="group">
                <input type="password" name="confirm_password" class="form-control<?php if(isset($show_error['confirm_password']))echo ' show-error';?>" value="<?php echo $confirm_password;?>" placeholder="Password again">
                <i class="fa fa-key"></i>
              </div>
              <div class="checkbox checkbox-primary">
                <input id="terms" name="terms" type="checkbox"<?php if($terms)echo ' checked="checked"';?>>
                <label for="terms"> By signing up you agree to our Terms of <a href="./terms/">Service</a> and <a href="./privacy/">Privacy Policy</a></label>
              </div>
              <button type="submit" class="btn btn-default btn-block">START REGISTRATION</button>
            </div>
            <input type="hidden" name="submit_form" value="1">
          </form>
          <div class="footer-links row">
            <div class="col-xs-6"><a href="login.php"><i class="fa fa-sign-in"></i> Login</a></div>
            <div class="col-xs-6 text-right"><a href="forgot.php"><i class="fa fa-lock"></i> Forgot password</a></div>
          </div>
        </div>
    </div>
</body>
</html>