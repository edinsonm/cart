<?php require_once('connections/dbsel.php'); ?>
<?php require_once('connections/comun.php'); ?>
<?php session_start(); 
if(!$_SESSION["products"])
header('Location: /');
/*
while ($post = each($_POST))
{
echo $post[0] . " = " . $post[1]."<br>";
}
*/
if (isset($_POST["MM_insert2"]) && ($_POST["MM_insert2"] == "form2")) 
{
if(isset($_SESSION["products"]) && count($_SESSION["products"])>0){ 
//if we have session variable
$total = 0;
$refVenta = time();
$total = $_SESSION["total"];
$pago = "Payu";
$Status = "PND";
$Datetime = date("Y-n-j").date(" H:i:s");

$mysqli_conn->query("INSERT INTO `orders`(`Id_order`, `Total`, `Pago`, `Status`, `Datetime`) VALUES ($refVenta, $total, '$pago', '$Status', '$Datetime')");
$BuyerName = $_POST['FirstName']." ".$_POST['LastName'];

$Depto = $_POST['Depto'];
$City = $_POST['City'];
$Address = $_POST['Address'];
$phone = $_POST['phone'];
$Email = $_POST['Email'];

$mysqli_conn->query("INSERT INTO `orders_ctc`(`Id_order`, `Buyer_name`, `Depto`, `City`, `Address`, `Phone`, `Email`, `Datetime`) 
VALUES ($refVenta, '$BuyerName', '$Depto', '$City', '$Address', '$phone', '$Email', '$Datetime')");

$Nombuy="";         
foreach($_SESSION["products"] as $product){ //loop though items and prepare html content
	
	//set variables to use them in HTML content below
	$name = $product["nombre"]; 
	$price = $product["precio"];
	$imagen = $product["imagen"];
	$allied_id = $product["allied_id"];
	$Id_product = $product["Id_product"];
	$prod_qty = $product["prod_qty"];
	$total_prod = $price*$prod_qty;
	$Nombuy.= $name." ";
	
$mysqli_conn->query("INSERT INTO `orders_pdt`(`Id_order`, `Id_allied`, `Id_product`, `Qty`, `Price`, `Total`, `Status`, `Datetime`) 
VALUES ($refVenta, $allied_id, '$Id_product', '$prod_qty', $price, $total_prod, 'NEW', '$Datetime')");
} 

$merchantId = "917984";
$accountId = "925148";
$llave_encripcion = "c9XSN5SVwc1BjNsuI7OVzCs2LU"; 
$prueba = "0"; //1 variable para poder utilizar tarjetas de crédito de prueba

$moneda = "COP"; //la moneda con la que se realiza la compra
$TaxIVA = 0;
$taxReturn=0;
$valor = number_format($total, 1, '.', '');

$llave = "$llave_encripcion~$merchantId~$refVenta~$valor~$moneda"; //concatenación para realizar la firma
$signature = md5($llave); //creación de la firma con la cadena previamente hecha

$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (!empty($_SERVER['HTTP_CLIENT_IP']))
	$Ip_ini = $_SERVER['HTTP_CLIENT_IP'];
else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	$Ip_ini =  $_SERVER['HTTP_X_FORWARDED_FOR'];
else $Ip_ini =  $_SERVER['REMOTE_ADDR'];

$mysqli_conn->query("INSERT INTO `transaction`(`Id_order`, `Nom_Payer`, `Dir_Payer`, `Tel_payer`, `Email_Payer`, `Descripcion`, `ValorTotal`,  
`Moneda`, `Firma`, `Estado`, `Channel_buy`, `IP_User`, `AgentUser`, `Datetime`) 
VALUES ($refVenta, '$BuyerName', '$Address', '$phone', '$Email', '$Nombuy', $total, '$moneda', '$signature', 'PENDING', 'Web', '$Ip_ini', '$user_agent', '$Datetime')");

unset($_SESSION["products"]);
unset($_SESSION["total"]);
unset($_SESSION["numitems"]);

//echo $merchantId."-".$refVenta."-".$valor."-".$moneda."-".$signature;
//Sandox https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu
//Prod https://gateway.payulatam.com/ppp-web-gateway
?>
 <form name="formpayu" method="post" action="https://gateway.payulatam.com/ppp-web-gateway">
  <input name="merchantId"    type="hidden"  value="<? echo $merchantId; ?>" >
  <input name="accountId"     type="hidden"  value="<? echo $accountId; ?>" >
  <input name="description"   type="hidden"  value="<? echo $Nombuy; ?>"  >
  <input name="referenceCode" type="hidden"  value="<? echo $refVenta; ?>" >
  <input name="amount"        type="hidden"  value="<? echo $valor; ?>" >
  <input name="tax"           type="hidden"  value="<? echo $TaxIVA; ?>">
  <input name="taxReturnBase" type="hidden"  value="<? echo $taxReturn; ?>">
  <input name="currency"      type="hidden"  value="<? echo $moneda; ?>" >
  <input name="signature"     type="hidden"  value="<? echo $signature; ?>">
  <input name="test"          type="hidden"  value="<? echo $prueba; ?>" >
  <input name="buyerEmail"    type="hidden"  value="<? echo $Email; ?>" >
  <input name="responseUrl"   type="hidden"  value="https://www.buyval.com/response_payu.php" >
  <input name="confirmationUrl"    type="hidden"  value="https://www.buyval.com/confirm_payu.php" >
</form>

<script type="text/javascript">
function submitform(){
  document.forms["formpayu"].submit();
}
submitform();
</script>
<? }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Buyval - eCommerce </title>

    <meta name="keywords" content="HTML5 Template" />
    <meta name="description" content="Buyval - eCommerce ">
    <meta name="author" content="SW-THEMES">
        
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/icons/favicon.jpg">
    
    
    <script type="text/javascript">
        WebFontConfig = {
            google: { families: [ 'Open+Sans:300,400,600,700,800','Poppins:300,400,500,600,700' ] }
        };
        (function(d) {
            var wf = d.createElement('script'), s = d.scripts[0];
            wf.src = 'assets/js/webfont.js';
            wf.async = true;
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>
    
    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/style.min.css">
    <link rel="stylesheet" href="assets/vendor/fontawesome-free/css/all.min.css">
</head>
<body>
    <div class="page-wrapper">
<?php include('header.php'); ?>

        <main class="main">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                        CATEGORIES</li>
                    </ol>
                </div><!-- End .container -->
            </nav>

            <div class="container">
                <ul class="checkout-progress-bar">
                    <li class="active">
                        <span>Envío</span>
                    </li>
                    <li>
                        <span>Pagar</span>
                    </li>
                </ul>
				<form method="post" enctype="multipart/form-data" action="" name="form1" id="form1" class="le-validate">
                <div class="row">
                    <div class="col-lg-8">
                        <ul class="checkout-steps">
                            <li>
                                <h2 class="step-title">Dirección Entrega</h2>
<!--
                                <form action="#">
                                    <div class="form-group required-field">
                                        <label>Email Address </label>
                                        <div class="form-control-tooltip">
                                            <input type="email" class="form-control" required>
                                            <span class="input-tooltip" data-toggle="tooltip" title="We'll send your order confirmation here." data-placement="right"><i class="icon-question-circle"></i></span>
                                        </div><!-- End .form-control-tooltip -->
 <!--                                   </div><!-- End .form-group -->

 <!--                                   <div class="form-group required-field">
                                        <label>Password </label>
                                        <input type="password" class="form-control" required>
                                    </div><!-- End .form-group -->
                                    
<!--                                    <p>You already have an account with us. Sign in or continue as guest.</p>
                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-primary">LOGIN</button>
                                        <a href="forgot-password.html" class="forget-pass"> Forgot your password?</a>
                                    </div><!-- End .form-footer -->
<!--                                </form>  -->

                                
                                    <div class="row">
									<div class="col-md-6 col-sm-6">
										<div class="form-group required-field">
											<label>Nombres </label>
											<input type="text" name="FirstName" class="form-control" required>
										</div><!-- End .form-group -->
									</div>
									<div class="col-sm-6">
										<div class="form-group required-field">
                                        <label>Apellidos </label>
                                        <input type="text" name="LastName" class="form-control" required>
                                    </div><!-- End .form-group -->
									</div>
									</div>
                                    <!--<div class="form-group">
                                        <label>Company </label>
                                        <input type="text" class="form-control">
                                    </div><!-- End .form-group -->
									<div class="row">
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
                                        <label>Departamento</label>
                                        <div class="select-custom">
                                            <select class="form-control" name="Depto">
                                                <option value="AMA">AMAZONAS</option>
												<option value="ANT">ANTIOQUIA</option>
												<option value="ARA">ARAUCA</option>
												<option value="ATL">ATLANTICO</option>
												<option value="BOL">BOLIVAR</option>
												<option value="BOY">BOYACÁ</option>
												<option value="CAL">CALDAS</option>
												<option value="CAQ">CAQUETÁ</option>
												<option value="CAS">CASANARE</option>
												<option value="CAU">CAUCA</option>
												<option value="CES">CESAR</option>
												<option value="CHO">CHOCÓ</option>
												<option value="CÓR">CÓRDOBA</option>
												<option value="CUN">CUNDINAMARCA</option>
												<option value="DIS">DISTRITO CAPITAL</option>
												<option value="GUA">GUAINÍA</option>
												<option value="GUV">GUAVIARE</option>
												<option value="HUI">HUILA</option>
												<option value="LAG">LA GUAJIRA</option>
												<option value="MAG">MAGDALENA</option>
												<option value="MET">META</option>
												<option value="NAR">NARIÑO</option>
												<option value="NOR">NORTE DE SANTANDER</option>
												<option value="PUT">PUTUMAYO</option>
												<option value="QUI">QUINDÍO</option>
												<option value="RIS">RISARALDA</option>
												<option value="SAP">SAN ANDRÉS Y PROVIDENCIA</option>
												<option value="SAN">SANTANDER</option>
												<option value="SUC">SUCRE</option>
												<option value="TOL">TOLIMA</option>
												<option value="VAL">VALLE</option>
												<option value="VAU">VAUPÉS</option>
												<option value="VIC">VICHADA</option>
                                            </select>
                                        </div><!-- End .select-custom -->
                                    </div><!-- End .form-group -->
									</div>
									<div class="col-sm-6">
										<div class="form-group required-field">
                                        <label>Ciudad</label>
                                        <input type="text" name="City" class="form-control" required>
										</div><!-- End .form-group -->
									</div>
									</div>
									<div class="row">
									<div class="col-md-6 col-sm-6">
										<div class="form-group required-field">
											<label>Dirección </label>
											<input type="text" name="Address" class="form-control" required>
										</div><!-- End .form-group -->
									</div>
									<div class="col-sm-6">
										<div class="form-group required-field">
                                        <label>Teléfono </label>
                                        <div class="form-control-tooltip">
                                            <input type="tel" name="phone" class="form-control" required>
                                            <span class="input-tooltip" data-toggle="tooltip" title="For delivery questions." data-placement="right"><i class="icon-question-circle"></i></span>
                                        </div><!-- End .form-control-tooltip -->
										</div><!-- End .form-group -->
									</div>
									</div>
									<div class="row">
									<div class="col-md-6 col-sm-6">
										<div class="form-group required-field">
											<label>E-mail </label>
											<input type="email" name="Email" class="form-control" required>
										</div><!-- End .form-group -->
									</div>
									</div>
									<!--
                                    <div class="form-group required-field">
                                        <label>Zip/Postal Code </label>
                                        <input type="text" class="form-control" required>
                                    </div><!-- End .form-group -->
									<!--
                                    <div class="form-group">
                                        <label>Pais</label>
                                        <div class="select-custom">
                                            <select class="form-control">
                                                <option value="USA">United States</option>
                                                <option value="Turkey">Turkey</option>
                                                <option value="China">China</option>
                                                <option value="Germany">Germany</option>
                                            </select>
                                        </div><!-- End .select-custom -->
                                    <!--</div><!-- End .form-group -->
                            </li>
<!--
                            <li>
                                <div class="checkout-step-shipping">
                                    <h2 class="step-title">Shipping Methods</h2>

                                    <table class="table table-step-shipping">
                                        <tbody>
                                            <tr>
                                                <td><input type="radio" name="shipping-method" value="flat"></td>
                                                <td><strong>$20.00</strong></td>
                                                <td>Fixed</td>
                                                <td>Flat Rate</td>
                                            </tr>

                                            <tr>
                                                <td><input type="radio" name="shipping-method" value="best"></td>
                                                <td><strong>$15.00</strong></td>
                                                <td>Table Rate</td>
                                                <td>Best Way</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!-- End .checkout-step-shipping -->
 <!--                           </li> -->
                        </ul>
                    </div><!-- End .col-lg-8 -->

                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h3>Resumen</h3>

                            <h4>
                                <a data-toggle="collapse" href="#order-cart-section" class="collapsed" role="button" aria-expanded="false" aria-controls="order-cart-section"><? echo $_SESSION["numitems"]; ?> artículos</a>
                            </h4>

                            <div class="collapse" id="order-cart-section">
                                <table class="table table-mini-cart">
                                    <tbody>
									<? if(isset($_SESSION["products"]) && count($_SESSION["products"])>0){ //if we have session variable
									$total = 0;
									foreach($_SESSION["products"] as $product){ //loop though items and prepare html content
										
										//set variables to use them in HTML content below
										$name = $product["nombre"]; 
										$price = $product["precio"];
										$imagen = $product["imagen"];
										$allied_id = $product["allied_id"];
										$Id_product = $product["Id_product"];
										$prod_qty = $product["prod_qty"];
										?>
                                        <tr>
                                            <td class="product-col">
                                                <figure class="product-image-container">
                                                    <a href="product?ID=<? echo $Id_product; ?>" class="product-image">
                                                        <img src="img/allies/<? echo $allied_id.'/catalog/'.$imagen; ?>" alt="product" width="178" height="178">
                                                    </a>
                                                </figure>
                                                <div>
                                                    <h2 class="product-title">
                                                        <a href="product?ID=<? echo $Id_product; ?>"><? echo $name; ?></a>
                                                    </h2>

                                                    <span class="product-qty">Cant: <? echo $prod_qty; ?></span>
                                                </div>
                                            </td>
                                            <td class="price-col">$<? echo $price; ?></td>
                                        </tr>
									<? }
									}	?>
                                    </tbody>    
                                </table>
                            </div><!-- End #order-cart-section -->
                        </div><!-- End .order-summary -->
                    </div><!-- End .col-lg-4 -->
                </div><!-- End .row -->

                <input type="hidden" name="MM_insert2" value="form2">
				<div class="row">
                    <div class="col-lg-8">
                        <div class="checkout-steps-action">
                            <input type="submit" class="btn btn-primary float-right" value="PAGAR ORDEN">
                        </div><!-- End .checkout-steps-action -->
                    </div><!-- End .col-lg-8 -->
                </div><!-- End .row -->
                </form>
            </div><!-- End .container -->

            <div class="mb-4"></div>

        </main><!-- End .main -->

        <?php include('footer.php'); ?><!-- End .footer -->
    </div><!-- End .page-wrapper -->

    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

    <?php include('mobile-menu.php'); ?>
	
    <a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

    <!-- Plugins JS File -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins.min.js"></script>
    <script src="assets/js/jquery.countdown/jquery.countdown.min.js"></script>
    <script src="assets/js/plugins/isotope-docs.min.js"></script>

    <!-- Main JS File -->   
    <script src="assets/js/main_init.min.js"></script>
    <script src="assets/js/main.min.js"></script>
</body>
<script>
$("#shopping-cart").on('click', 'a.btn-remove', function(e) {
		e.preventDefault(); 
		var pcode = $(this).attr("data-code"); //get product code
		$(this).parent().fadeOut(); //remove item element from box
		//$('#itemlist_'+pcode).remove();
		$.ajax({
			type: 'GET',
			url: 'cart_setting',
			data:{"remove_code":pcode},
			success:function(data){		
				//$('#shopping-cart').html(data);
				loadcart();
				//loaddetail();
				//loadtotal();
			}
		});	
	});
		
	function loadcart(){
		$(document).ready(function(){
			$.ajax({
			type: 'GET',
			url: 'cart_setting',
			data:{"bag":1},
			success:function(data){				
			if (data != '') {				
				$('#shopping-cart').html(data);
				}
			else $('#shopping-cart').html('<div class="dropdown cart-dropdown"><a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static"><i class="minicart-icon"></i></a></div>')
			}
			});
		});
	 }
	 loadcart();
	 </script>
</html>