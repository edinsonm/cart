<?php require_once('connections/dbsel.php'); ?>
<?php require_once('connections/comun.php'); ?>
<?php
session_start();  

$llave_encripcion = "c9XSN5SVwc1BjNsuI7OVzCs2LU";
$merchantId = $_REQUEST['merchantId'];
//Es la firma digital creada para cada uno de las transacciones.
$Firma_Tx = $_REQUEST['signature'];
//Es la referencia de la venta o pedido. Deber ser único por cada transacción que se envía al sistema.
$Ref_venta = $_REQUEST['referenceCode'];
//Es el monto total de la transacción. Puede contener dos dígitos decimales. Ej. 10000.00 ó 10000
$Valor_Tx = $_REQUEST['TX_VALUE'];
$Valor_Tx = number_format($Valor_Tx, 1, '.', '');
//La moneda respectiva en la que se realiza el pago. El proceso de conciliación se hace en pesos a la tasa representativa del día.
$Moneda = $_REQUEST['currency'];
//Indica el estado de la transacción en el sistema.
$TransactionState = $_REQUEST['transactionState'];
//comparacion de las firmas para comprobar que los datos si vienen de Payu
$firma_cadena = "$llave_encripcion~$merchantId~$Ref_venta~$Valor_Tx~$Moneda~$TransactionState";
$firmacreada = md5($firma_cadena);//se genera nuevamente la firma para validar

if(strtoupper($Firma_Tx)==strtoupper($firmacreada)){
//El riesgo asociado a la transacción. Toma un valor entre 0 y 1. A mayor riesgo mayor valor. Viene en formato ###.00
$Riesgo = $_GET['risk']; 
//El código de respuesta.
$CodigoRta_POL = $_GET['polResponseCode'];
//La referencia o número de la transacción generado en PayU.
$Referencia_POL = $_GET['reference_pol']; 
//El identificador interno del medio de pago utilizado.
$MedioPago_POL = $_GET['polPaymentMethod']; 
//El tipo de medio de pago utilizado para el pago.
$TipoPago_POL = $_GET['polPaymentMethodType']; 
//Número de cuotas en las cuales se difirió el pago con tarjeta crédito.
$Cuotas_POL = $_GET['installmentsNumber']; 
//Es el valor del IVA de la transacción, si se envía el IVA nulo el sistema aplicará el 19% automáticamente. Puede contener dos dígitos decimales. Ej: 19000.00. En caso de no tener IVA debe enviarse en 0.
$IVA_POL = $_GET['TX_TAX']; 
//Campo que contiene el correo electrónico del comprador para notificarle el resultado de la transacción por correo electrónico.
$Email_POL = $_GET['buyerEmail']; 
//El cus, código único de seguimiento, es la referencia del pago dentro del Banco, aplica solo para pagos con PSE.
$Cus_POL = $_GET['cus']; 
//El nombre del banco, aplica solo para pagos con PSE.
$BancoPSE_POL = $_GET['pseBank']; 
//Idioma en el que se desea mostrar la pasarela de pagos.
$Idioma_POL = $_GET['lng']; 
//Es la descripción de la venta.
$Descripcion = $_GET['description']; 
//Código de respuesta que entrega PayU.
$LapResponseCode = $_GET['lapResponseCode']; 
//Medio de pago con el cual se hizo el pago por ejemplo VISA.
$LapPaymentMethod = $_GET['lapPaymentMethod']; 
//Tipo de medio de pago con el que se realiza por ejemplo CREDIT_CARD.
$LapPaymentMethodType = $_GET['lapPaymentMethodType']; 
$LapTransactionState = $_GET['lapTransactionState'];
//	Descripción del estado de la transacción.
$Mensaje_Tx = $_GET['message']; 
//Campo adicional para enviar información sobre la compra. Ej. Códigos internos de los productos.
$Extra1_POL = $_GET['extra1']; 
$Extra2_POL = $_GET['extra2'];
$Extra3_POL = $_GET['extra3'];
//Código de autorización de la venta.
$CodAut_POL = $_GET['authorizationCode']; 
//Identificador generado por PSE.
$CicloPSE_POL = $_GET['pseCycle']; 
//Referencia no. 1 para pagos con PSE.
$PSE_Ref1 = $_GET['pseReference1']; 
$PSE_Ref2 = $_GET['pseReference2'];
$PSE_Ref3 = $_GET['pseReference3'];
//Identificador de la transacción.
$TransactionId = $_GET['transactionId']; 
//Código de seguimiento de la venta en el sitio del comercio.
$TrazabilityCode = $_GET['trazabilityCode']; 

$Fecha=date("Y-n-j").date(" H:i:s");

if ($_REQUEST['transactionState'] == 4 ) {
	$estadoTx = "Transacci&oacute;n aprobada"; }
else if ($_REQUEST['transactionState'] == 6 ) {
	$estadoTx = "Transacci&oacute;n rechazada";}
else if ($_REQUEST['transactionState'] == 104 ) {
	$estadoTx = "Error";}
else if ($_REQUEST['transactionState'] == 7 ) {
	$estadoTx = "Transacci&oacute;n pendiente";}
else if ($_REQUEST['transactionState'] == 5 ) {
	$estadoTx = "Transacci&oacute;n abandonada";}
else {
	$estadoTx=$Mensaje_Tx;
}
}
else $estadoTx='Error validando firma digital.';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Buyval - Resultado transacción</title>

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
        <?php include('header.php'); ?><!-- End .header -->

        <main class="main">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                        checkout</li>
                    </ol>
                </div><!-- End .container -->
            </nav>

            <div class="container">
                <ul class="checkout-progress-bar">
                    <li class="active">
                        <span>Respuesta pago</span>
                    </li>
                </ul>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h3>Resumen</h3>
							<? $resultpro = $mysqli_conn->query("SELECT o.*, p.Nombre,i.Source FROM orders_pdt o, products p, products_img i 
							WHERE Id_order = '$Ref_venta' AND o.Id_product = p.Id_product AND p.Id_product=i.Id_product AND i.Main=1");
							$numprod = mysqli_num_rows($resultpro);
                            ?>
							<h4>
                                <a data-toggle="collapse" href="#order-cart-section" class="" role="button" aria-expanded="false" aria-controls="order-cart-section"><? echo $numprod; ?> artículos</a>
                            </h4>

                            <div class="collapse show" id="order-cart-section">
                                <table class="table table-mini-cart">
                                    <tbody>
                                        <tr>
                                        <? while($rowProd = mysqli_fetch_assoc($resultpro)){ //loop though items and prepare html content
										//set variables to use them in HTML content below
										$name = $rowProd["Nombre"]; 
										$price = $rowProd["Price"];
										$imagen = $rowProd["Source"];
										$allied_id = $rowProd["Id_allied"];
										$Id_product = $rowProd["Id_product"];
										$prod_qty = $rowProd["Qty"];
										?>
											<td class="product-col">
                                                <figure class="product-image-container">
                                                    <a href="product?ID=<? echo $Id_product; ?>" class="product-image">
                                                        <img src="img/allies/<? echo $allied_id.'/catalog/'.$imagen; ?>" alt="product" width="73" height="73">
                                                    </a>
                                                </figure>
                                                <div>
                                                    <h2 class="product-title">
                                                        <a href="product?ID=<? echo $Id_product; ?>"><? echo $name; ?></a>
                                                    </h2>

                                                    <span class="product-qty">Cantidad: <? echo $price; ?></span>
                                                </div>
                                            </td>
										<? } ?>
                                            <td class="price-col"><? echo $price; ?></td>
                                        </tr>
                                    </tbody>    
                                </table>
                            </div><!-- End #order-cart-section -->
                        </div><!-- End .order-summary -->

                        <div class="checkout-info-box">
                            <h3 class="step-title">Envío:
                               <!-- <a href="#" title="Edit" class="step-title-edit"><span class="sr-only">Edit</span><i class="icon-pencil"></i></a>-->
                            </h3>
							<? $resultcnt = $mysqli_conn->query("SELECT * FROM orders_ctc WHERE Id_order = '$Ref_venta'");
							$rowcnt = mysqli_fetch_assoc($resultcnt)
                            ?>
                            <address>
                                <? echo $rowcnt['Buyer_name']; ?> <br>
                                <? echo $rowcnt['Address']; ?> <br>
                                <? echo $rowcnt['City']; ?>, <? echo $rowcnt['Depto']; ?> <br>
                                <? echo $rowcnt['Email']; ?> <br>
                                <? echo $rowcnt['Phone']; ?>
                            </address>
                        </div><!-- End .checkout-info-box -->

                        <!--<div class="checkout-info-box">
                            <h3 class="step-title">Shipping Method: 
                                <a href="#" title="Edit" class="step-title-edit"><span class="sr-only">Edit</span><i class="icon-pencil"></i></a>
                            </h3>

                            <p>Flat Rate - Fixed</p>
                        </div><!-- End .checkout-info-box -->
                    </div><!-- End .col-lg-4 -->

                    <div class="col-lg-8 order-lg-first">
                        <div class="checkout-payment">
                            <h2 class="step-title">Resumen transacción:</h2>

                            <table class="table table_summary">
							  <!--DWLayoutTable-->
							 <tbody>
							<tr>
							<td>Fecha de procesamiento:</td>
							<td><?php echo $Fecha; ?></td>
							</tr>
							<tr>
							<td>Estado de la transacci&oacute;n:</td>
							<td><?php echo $estadoTx; ?> </td>
							</tr>
							<tr>
							<td>Referencia de la venta:</td>
							<td><?php echo $Ref_venta; ?> </td> </tr>
							<tr>
							<td>Referencia de la transacci&oacute;n:</td>
							<td><?php echo $Referencia_POL; ?> </td>
							</tr>
							<tr>
							<?php
							if($banco_pse!=null){
							?>
							<tr>
							<td>CUS:</td>
							<td><?php echo $cus; ?> </td>
							</tr>
							<tr>
							<td>Banco:</td>
							<td><?php echo $banco_pse; ?> </td>
							</tr>
							<?php
							}
							?>
							<tr>
							<td>Entidad:</td>
							<td><?php echo $LapPaymentMethod; ?> </td>
							</tr>
							
							<tr class="total">
							<td>
								Total
							</td>
							<td class="text-right">
							<? 
							echo "$".number_format($Valor_Tx)." ".$Moneda; ?>
							</td>
							</tr>
							</tbody>
							</table>

                            <div class="clearfix">
                                <a href="/" class="btn btn-primary float-right">Continuar</a>
                            </div><!-- End .clearfix -->
                        </div><!-- End .checkout-payment -->
                    </div><!-- End .col-lg-8 -->
                </div><!-- End .row -->
            </div><!-- End .container -->

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
    <script src="assets/js/plugins/isotope-docs.min.js"></script>

    <!-- Main JS File -->   
    <script src="assets/js/main.min.js"></script>
</body>
</html>