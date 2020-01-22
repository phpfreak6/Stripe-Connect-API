<?php
error_reporting(-1);
session_start();

require_once('config.php');
// require_once('includes/functions.php');
require_once('stripe/n/init.php');

require_once('session.php');

require_once('config-master-no-db.php');

require_once('config-breeze-5.php');

require_once('includes/functions.php');

require_once('includes/dispatch_functions.php');

require_once('includes/paging.php');

?>
<?php
function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
 
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d;
        }
    }

	
	
	function array_to_obj($array, &$obj)
  {
    foreach ($array as $key => $value)
    {
      if (is_array($value))
      {
      $obj->$key = new stdClass();
      array_to_obj($value, $obj->$key);
      }
      else
      {
        $obj->$key = $value;
      }
    }
  return $obj;
  }

function arrayToObject($array)
{
 $object= new stdClass();
 return array_to_obj($array,$object);
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


\Stripe\Stripe::setApiKey('sk_test_9TzWZWto24zBpqUnhSki3Vjc');

$sdate = new DateTime();
$stimestamp =  $sdate->getTimestamp();
$sip =   $_SERVER['REMOTE_ADDR'];

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM `availcal_users` WHERE id='$user_id' LIMIT 1";
$rs  = mysql_query($sql, $con2)or die(mysql_error($con2));
$num = mysql_num_rows($rs);
$userifo = mysql_fetch_assoc($rs);
$scountry     		=  $userifo['country'];
$accounttype  		=  $userifo['stripeaccounttype'];
$stripemangedid  	=  $userifo['stripemangedid'];
$scurrency   		=  $userifo['currency'];
$first_name   		=  $userifo['firstname'];
$last_name    		=  $userifo['lastname'];

$dob      = explode("-",$userifo['dob']);
$date     =  $dob[0];
$month    =  $dob[1];
$year     =  $dob[2];


// $account = \Stripe\Account::retrieve("acct_18zR6eK8yl0rIlic");

// echo "<pre>";

// print_r($account);

// echo "</pre>";



// exit;

// echo "<pre>";
// print_r($userifo);
// echo "</pre>";
/* */
// $scountry     =  'US';
// $scountry     =  'GB';
// $accounttype =  'sole_prop';
// $accounttype =  'corporation';
// $scurrency = 'GBP';
// $scurrency = 'USD'; 
// echo "-->".$stripemangedid."<---";
$sqld = "SELECT * FROM `availcal_users_details` WHERE user_id='$user_id' LIMIT 1";
$rsd  = mysql_query($sqld, $con2)or die(mysql_error($con2));
$userifod = mysql_fetch_assoc($rsd);
$userinfo = (array)json_decode($userifod['data']);
//echo '<pre>' ; print_r($userinfo); die;

if($scountry == 'GB' && $accounttype == 'sole_prop'){
	include("account-inc-1.php");
}
if($scountry == 'GB' && $accounttype != 'sole_prop'){
	include("account-inc-2.php");
}
if($scountry == 'US' && $accounttype == 'sole_prop'){
	include("account-inc-3.php");
}
if($scountry == 'US' && $accounttype != 'sole_prop'){
	include("account-inc-4.php");
}


/* if(isset($_POST['submit_form'])){
	if($_POST['ftype'] == '1'){
		include("account-inc-1.php");
	}
	else if($_POST['ftype'] == '2'){
		include("account-inc-2.php");
	}
	else if($_POST['ftype'] == '3'){
		include("account-inc-3.php");
	}
	else if($_POST['ftype'] == '4'){
		include("account-inc-4.php");
	}
}
 */


?> 
<!DOCTYPE html>

<html lang="en">

  <head>

  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="description" content="Kode is a Premium Bootstrap Admin Template, It's responsive, clean coded and mobile friendly">

  <meta name="keywords" content="bootstrap, admin, dashboard, flat admin template, responsive," />

  <title>Service Charges</title>



  <!-- ========== Css Files ========== -->

  <link href="css/root.css" rel="stylesheet">

  <link rel="stylesheet" href="custom.css" type="text/css" title="no title" charset="utf-8"/>

  </style>
  </head>

  <body>

  <!-- Start Page Loading -->

  <div class="loading"><img src="img/loading.gif" alt="loading-img"></div>

  <!-- End Page Loading -->



	<?php include('topbar.php');?>

    

    <?php include('sidebar.php');?>



 <!-- //////////////////////////////////////////////////////////////////////////// --> 

<!-- START CONTENT -->

<div class="content">



  <!-- Start Page Header -->

  <div class="page-header">

    <h1 class="title">Manage Account  </h1>
	
  </div>

  <!-- End Page Header -->


    <div class="container-padding">

<!-- Start row -->

  <div class="row">



<div class="col-md-12">


        <!--div class="login-form row vertical-center-row" style="width:600px"-->
          <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
            <div class="top">
              <!--img src="img/logo_swish.png" alt="icon" class="icon"-->
              <?php  if(count($show_error)):?>
                <?php echo '<p>- '.implode('</p><p>- ', $show_error).'</p>';?>
              <?php endif; ?>
            </div>
            <div class="form-area" style="width: 800px;">		
				<?php				
				if($scountry == 'GB' && $accounttype == 'sole_prop'){
					include("type1.php");
				}
				if($scountry == 'GB' && $accounttype != 'sole_prop'){
					include("type2.php");
				}
				if($scountry == 'US' && $accounttype == 'sole_prop'){
					include("type3.php");
				}
				if($scountry == 'US' && $accounttype != 'sole_prop'){
					include("type4.php");
				}
				?>
				<input type="hidden" name="submit_form" value="3">				
				<div class="col-xs-12"><label>&nbsp;</label><button type="submit" class="subbtn btn btn-default btn-block">Submit</button></div>	
            </div>
          </form>
       
    </div>  <br ><br >  </div>






<style>
.login-form form .form-area .fa {
  top: 40px !important;
}
.login-form form .form-area select.form-control {
  padding-left: 10px;;
}
.login-form form .form-area .form-control {
  padding-left: 10px;
}

.row > h4 {
  line-height: 0;
  padding: 0 15px;
}
.row > h5 {
  line-height: 0;
  padding: 0 15px;
}

.radio-inline > input {
  margin-top: -5px !important;
}

.bs-wizard {margin-top: 40px;}

/*Form Wizard*/
.bs-wizard {border-bottom: solid 1px #e0e0e0; padding: 0 0 10px 0;}
.bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
.bs-wizard > .bs-wizard-step + .bs-wizard-step {}
.bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; font-size: 16px; margin-bottom: 5px;}
.bs-wizard > .bs-wizard-step .bs-wizard-info {color: #999; font-size: 14px;}
.bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 30px; height: 30px; display: block; background: #c7ebf9; top: 45px; left: 50%; margin-top: -15px; margin-left: -15px; border-radius: 50%;} 
.bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 14px; height: 14px; background: #009dde; border-radius: 50px; position: absolute; top: 8px; left: 8px; } 
.bs-wizard > .bs-wizard-step > .progress {position: relative; border-radius: 0px; height: 8px; box-shadow: none; margin: 20px 0;}
.bs-wizard > .bs-wizard-step > .progress > .progress-bar {width:0px; box-shadow: none; background: #c7ebf9;}
.bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {width:100%;}
.bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}
.bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
.bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 100%;}
.bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {background-color: #f5f5f5;}
.bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {opacity: 0;}
.bs-wizard > .bs-wizard-step:first-child  > .progress {left: 50%; width: 50%;}
.bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}
.bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot{ pointer-events: none; }
/*END Form Wizard*/

.wknone{
	display:none;
}
.form-area hr {
  margin: 5px 0 ;
}
.top p {
  color: red !important;
}
</style>

</div>

<!-- END CONTAINER -->

 <!-- //////////////////////////////////////////////////////////////////////////// --> 





<!-- Start Footer -->

<div class="row footer">

  <div class="col-md-6 text-left">

  Copyright © 2015 <a href="http://themeforest.net/user/egemem/portfolio" target="_blank">Egemem</a> All rights reserved.

  </div>

  <div class="col-md-6 text-right">

    Design and Developed by <a href="http://themeforest.net/user/egemem/portfolio" target="_blank">Egemem</a>

  </div> 

</div>

<!-- End Footer -->



<?php include('common-modal.php');?>



</div>


<!-- End Content -->

 <!-- //////////////////////////////////////////////////////////////////////////// --> 
 
 
<!-- ================================================

jQuery Library

================================================ -->

<script type="text/javascript" src="js/jquery.min.js"></script>

<script src="js/datatables/datatables.min.js"></script>



<script>
$(document).ready(function() {
  $("#example0").dataTable({
	// "sPaginationType": "full_numbers",
	"pageLength": 50,
	"bSort": false,
	 "bLengthChange": false ,
	// "bFilter": true,
	"sDom":"lrtip" // default is lfrtip, where the f is the filter
   });
  var oTable;
  oTable = $('#example0').dataTable();

  $('#msds-select').change( function() { 
		oTable.fnFilter( $(this).val() ); 
   });
  $('#msds-select1').change( function() { 
		oTable.fnFilter( $(this).val() ); 
   });
});
   
   
/* $(document).ready(function() {
    $('#example0').DataTable( {
        "pageLength": 1,
		"bSort": false
		
    } );
} ); */
</script>

<!-- ================================================

Bootstrap Core JavaScript File

================================================ -->

<script src="js/bootstrap/bootstrap.min.js"></script>



<!-- ================================================

Plugin.js - Some Specific JS codes for Plugin Settings

================================================ -->

<script type="text/javascript" src="js/plugins.js"></script>



<!-- ================================================

Bootstrap Select

================================================ -->

<script type="text/javascript" src="js/bootstrap-select/bootstrap-select.js"></script>



<!-- ================================================

Moment.js

================================================ -->

<script type="text/javascript" src="js/moment/moment.min.js"></script>



<!-- ================================================

Bootstrap Date Range Picker

================================================ -->

<script type="text/javascript" src="js/date-range-picker/daterangepicker.js"></script>





<script type="text/javascript">



setInterval(function(){ 



var url = 'noti-ajax.php';

$.ajax({

  type: 'GET',

  url: url,

  success: function(data){
	  var s =  data.split("~!~");
	  
	$('.ncount').text(s[0]);
	$('.nmsg').html(s[1]);

  }

});




 }, 5000);







$(document).ready(function(){



  $('.prices').click(function(e){

  	var id = $(this).attr('data-id');

	if(id!=''){

		var url = 'ajax/breeze.ajax.php?task=get_prices&id='+id;

		$.ajax({

		  type: 'GET',

		  url: url,

		  success: function(data){

			$('#common_modal_content').html(data).css('height', '150px');

			$('#common_modal').trigger('click');

		  }

		});

	}

	//alert(contact_id);

	e.preventDefault();

  });

  

  $('#reset').click(function(e){

  	window.location.href = 'items.php';

  	e.preventDefault();

  });

  

  $('.edit').click(function(e){

  	var table = $(this).attr('data-table');

  	var id = $(this).attr('data-id');

  	var size = $(this).attr('data-size');

	

	if(table == 'price' || table == 'code'){

		 $('#modal_close').trigger('click');

	}

	

	_get_ajax_form(table, id, size);

  	e.preventDefault();

  });

  

  $('#common_modal_content').on("click",".edit",function(e){

  	var table = $(this).attr('data-table');

  	var id = $(this).attr('data-id');

  	var size = $(this).attr('data-size');

	

	 $('#modal_close').trigger('click');

	

	_get_ajax_form(table, id, size);

  	e.preventDefault();

  });



  $('#service_type').change(function(){

  	var service_type = $("#service_type").val();

	if(service_type == 'new-service-type'){

		// show popup

		var table = 'service_type';

		var id = 0;

		

		_get_ajax_form(table, id, '200');

	}

	else{

		_reaload_page();

	}

  });

  $('#item').change(function(){

  	var item = $("#item").val();

	if(item == 'new-item'){

		// show popup

		var table = 'item';

		var id = 0;

		_get_ajax_form(table, id, '300');

	}

	else{

		_reaload_page();

	}

  });

  $('#name').change(function(){

  	var name = $("#name").val();

	if(name == 'new-name'){

		// show popup

		var table = 'name';

		var id = 0;

		_get_ajax_form(table, id, '450');

	}

	else{

		_reaload_page();

	}

  });

  $('#unit').change(function(){

  	var unit = $("#unit").val();

	if(unit == 'new-unit'){

		// show popup

		var table = 'unit';

		var id = 0;

		_get_ajax_form(table, id, '300');

	}

	else{

		_reaload_page();

	}

  });

  $('#common_modal_content').on("click","#add_price",function(e){

	  $('#modal_close').trigger('click');

	  	var cid = $(this).attr('data-cid');

		var table = 'price';

		var id = 0;

		_get_ajax_form(table, id, '300', cid);

		e.preventDefault();

  });

  $('#common_modal_content').on("click","#add_code",function(e){

	  $('#modal_close').trigger('click');

		var table = 'code';

		var id = 0;

		_get_ajax_form(table, id, '300');

		e.preventDefault();

  });

  $('#common_modal_content').on("click",".delete-price",function(e){

  

  	var conf = confirm('Are you sure you want to delete this item?');

	

	if(conf == true)

	{

  		var id = $(this).attr('data-id');

		

		var url = 'ajax/breeze.ajax.php?task=delete_price&id='+id;



		$.ajax({

           type: "GET",

           url: url,

           success: function(data) {

		   		//alert(data);

			   if(data=='ok'){

				   //$('#modal_close').trigger('click');

				   $('#price-'+id).remove();

			   }

			   else{

			   	alert(data);

			   }

           }

         });

	}

		

	e.preventDefault();

  });

  $('#common_modal_content').on("click","#save_form",function(e){

  

  		var table = $('#table').val();

		var cid = $('#cid').val();

		var data = $('#edit_form').serialize();

		var url = 'ajax/breeze.ajax.php?task=edit';



		$.ajax({

           type: "POST",

           url: url,

           data: data, // serializes the form's elements.

           success: function(data) {

			   if(data=='ok'){

				   $('#modal_close').trigger('click');

				   if(table != 'price' && table != 'code'){

					   window.location.reload(true);

				   }

				   if(table == 'price' && cid!=''){

						$('#price-'+cid).trigger('click');

				   }

			   }

			   else{

			   	alert(data);

			   }

           }

         });



		e.preventDefault();

	});

  $('#common_modal_content').on("click","#cancel_form",function(e){

		$('#modal_close').trigger('click');

		e.preventDefault();

	});

  $('#common_modal_content').on("click","#copy_item",function(e){

  

  		var table = $(this).attr('data-table');

  		var id = $(this).attr('data-id');

		var url = 'ajax/breeze.ajax.php?task=copy&table='+table+'&id='+id;



		$.ajax({

           type: "GET",

           url: url,

           success: function(data) {

		   		var resp = data.split("-");

			   if(resp[0]=='ok'){

				   $('#modal_close').trigger('click');

				   _get_ajax_form(table, resp[1], '450');

			   }

			   else{

			   	alert(data);

			   }

           }

         });



		e.preventDefault();

	});

	

	$('#common_modal_content').on('click', '.page-item', function(e){

		var href = $(this).attr('href').replace('#', '');

		var url = 'ajax/breeze.ajax.php?task=get_prices';

		if(href!='')

			url += '&'+href;

			

		load_page(url, '#common_modal_content');	

		

		e.preventDefault();

		

		

	});



	

});



function _get_ajax_form(table, id, size, cid=''){

	var url = 'ajax/breeze.ajax.php?task=get_form&table='+table+'&id='+id;

	if(cid!=''){

		url+='&cid='+cid;

	}

	$.ajax({

	  type: 'GET',

	  url: url,

	  success: function(data){

		$('#common_modal_content').html(data).css('height', size+'px');

		$('#common_modal').trigger('click');

		$('#STATUS').selectpicker('refresh');

		if(table == 'code'){

			//$('#LOCATIONS').selectpicker('refresh');

			$('#LOCATIONS').selectpicker();

		}

	   if(table == 'code'){

		   $('#EXPIREDATE').daterangepicker({ singleDatePicker: true, format: 'DD/MM/YYYY'}, function(start, end, label) {});

	   }



	  }

	});

	//alert(url);

}



function _reaload_page(){

	var service_type = $("#service_type").val();

	var item = $("#item").val();

	var name = $("#name").val();

	var unit = $("#unit").val();

	

	var url = 'items.php';

		

	var parts = [];

	

	

	if(service_type!="")

	{

		parts.push('service_type='+service_type);

	}

	if(item!="")

	{

		parts.push('item='+item);

	}

	if(name!="")

	{

		parts.push('name='+name);

	}

	if(unit!="")

	{

		parts.push('unit='+unit);

	}

	

	if(parts.length>0){

		url +='?'+parts.join('&');

	}

	window.location.href = url;

}



function load_page(url, cont_div){



	$.ajax({

	  type: 'GET',

	  url: url,

	  success: function(data){

		$(cont_div).html(data);

	  }



	});



}



</script>

 
 <!-- //////////////////////////////////////////////////////////////////////////// --> 



</body>

</html>

<?php

/*

mysql_close($con1);

mysql_close($con2);

mysql_close($con3);

*/

mysql_close($con5);

?>