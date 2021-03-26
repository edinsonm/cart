<?php require_once('connections/dbsel.php'); ?>
<?php include('connections/comun.php'); ?>
<?php require_once('admin/mail_sender.php'); ?>
<?php

//additional_value=0.00;reference_sale=1606268324;risk=0.0;pseCycle=null;airline_code=;account_id=33506;response_message_pol=ANTIFRAUD_REJECTED;payment_method_id=4;email_buyer=edinson_morales@hotmail.com;office_phone=;payment_method_name=PSE;response_code_pol=23;billing_country=CO;value=8500.00;installments_number=;nickname_seller=;shipping_address=;currency=COP;administrative_fee_base=0.00;merchant_id=29462;description=Articulo 2;customer_number=;error_code_bank=;shipping_country=CO;error_message_bank=;phone=;billing_city=;state_pol=6;pse_bank=DAVIPLATA;antifraudMerchantId=;tax=0.00;reference_pol=1170531926;ip=179.32.188.203;exchange_rate=1.00;test=0;transaction_date=2020-11-24 20:39:07;transaction_id=9c535090-b652-4476-b314-6faa81fed565;attempts=1;payment_method=25;nickname_buyer=;bank_id=25;administrative_fee_tax=0.00;administrative_fee=0.00;extra1=;billing_address=;payment_request_state=R;operation_date=2020-11-24 20:39:07;extra3=;extra2=;sign=b2cd755b45174556c879e0e38d682cd4;bank_referenced_name=;shipping_city=;pse_reference1=179.32.188.203;pse_reference2=CC;payment_method_type=4;pse_reference3=13749427;date=2020.11.24 08:39:07;
$MerchantId = $_POST['merchant_id'];
$Ref_venta = $_POST['reference_sale'];
$CodigoRta = $_POST['response_code_pol'];
$Riesgo = $_POST['risk'];
$Estado = $_POST['state_pol'];
$Referencia = $_POST['reference_pol'];
$Firma = $_POST['sign'];
$Payment_method = $_POST['payment_method'];
$TipoPago = $_POST['payment_method_type'];
$Cuotas = $_POST['installments_number'];
$ValorTx = $_POST['value'];
$ValorTx = number_format($ValorTx, 1, '.', '');
$IVA = $_POST['tax'];
$Email = $_POST['email_buyer'];
$FecTran = $_POST['transaction_date'];
$Moneda = $_POST['currency'];
$BancoPSE = $_POST['pse_bank'];
$Descripcion = $_POST['description'];
//Medio de pago con el cual se hizo el pago por ejemplo VISA.
$Payment_method_name = $_POST['payment_method_name']; 
$Payment_request_state = $_POST['payment_request_state'];
$Bank_id = $_POST['bank_id'];
$Payment_method = $_POST['payment_method'];
$Attempts = $_POST['attempts'];
$CC_holder = $_POST['cc_holder'];
$CC_number = $_POST['cc_number'];
$Error_code_bank = $_POST['error_code_bank'];
//Descripción del estado de la transacción.
$Mensaje = $_POST['response_message_pol']; 
$Extra1 = $_POST['extra1'];
$Extra2 = $_POST['extra2'];
$PSE_Ref1 = $_POST['pse_reference1'];
$PSE_Ref2 = $_POST['pse_reference2'];
$PSE_Ref3 = $_POST['pse_reference3'];
//Identificador de la transacción.
$TransactionId = $_POST['transaction_id']; 
//Código de seguimiento de la venta en el sitio del comercio.
$IP_User = $_POST['ip']; 

$llave_encripcion = "c9XSN5SVwc1BjNsuI7OVzCs2LU";
$firma_cadena = "$llave_encripcion~$MerchantId~$Ref_venta~$ValorTx~$Moneda~$Estado";
$firmacreada = md5($firma_cadena);//firma que generaron ustedes

$Log = "";
foreach($_POST as $nombre_campo => $valor){ 
   $Log .= $nombre_campo."=".$valor.";"; 
}

$Fecha=date("Y-n-j").date(" H:i:s");
//comparacion de las firmas para comprobar que los datos si vienen de PayU


$mysqli_conn->query("UPDATE transaction SET Tx_state='$Estado', Riesgo='$Riesgo', CodigoRta='$CodigoRta', TipoPago='$TipoPago', Referencia='$Referencia', Firma='$Firma', 
Extra1='$Extra1', Extra2='$Extra2', Cuotas='$Cuotas', ValorPvd='$ValorTx', IVA='$IVA', MedioPago='$Franchise', Moneda='$Moneda', FecTx='$FecTran', 
CodAut='$CodAut', TransactionId='$TransactionId', BancoPSE='$BancoPSE', Payment_request_state= '$Payment_request_state', 
Bank_id='$Bank_id', Payment_method='$Payment_method', Attempts='$Attempts', CC_holder='$CC_holder', CC_number='$CC_number', CC_number='$CC_number', 
Error_code_bank='$Error_code_bank', Email_Payer='$Email', Mensaje_Tx='$Mensaje', PSE_Ref1='$PSE_Ref1', PSE_Ref2='$PSE_Ref2', 
PSE_Ref3='$PSE_Ref3', IP_User='$IP_User', Datetime='$Fecha' WHERE Id_order='$Ref_venta'");

$mysqli_conn->query("UPDATE transaction SET Log = '$Log' WHERE Id_order = '$Ref_venta'");

if(strtoupper($Firma)==strtoupper($firmacreada)){
//if (($Estado != 4) && ($CodigoRta != 1)){ //Cuando no es exitoso
if (($Estado == 4) && ($CodigoRta == 1)){  //Cuando es exitoso
	$Email = strtolower(htmlentities($Email, ENT_QUOTES));    
	$mysqli_conn->query("UPDATE orders SET Status = 'PAY' WHERE Id_order = '$Ref_venta'");
	$mysqli_conn->query("UPDATE transaction SET Estado = 'APPROVED' WHERE Id_order = '$Ref_venta'");
	//$result = $mysqli_conn->query("SELECT p.Nombre, op.Qty, op. Price, op.Total FROM orders_pdt op, products p WHERE op.Id_product=p.Id_product AND op.Id_order='$Ref_venta' ORDER BY op.Id_allied");
	$result = $mysqli_conn->query("SELECT p.Nombre, op.Qty, op. Price, op.Total, op.Id_allied FROM orders_pdt op, products p WHERE op.Id_product=p.Id_product AND op.Id_order='$Ref_venta' ORDER BY op.Id_allied");
	
	$Text3 = "";
	while($row = mysqli_fetch_assoc($result))
	{
	$Text3 .= "<table border='1'><th><tr>Articulo</tr><tr>Precio</tr><tr>Cantidad</tr><tr>Subtotal</tr></th><tr><td>".$row['Nombre']."</td><td>".$row['Price']."</td><td>".$row['Qty']."</td><td>".$row['Total']."</td></tr></table>";	
	$Id_allied = $row['Id_allied'];
	
	$mysqli_conn->query("SELECT Email FROM alliesusers WHERE Id_allied = '$Id_allied'");
	$ImagenPpal = "";
	$Asunto="Venta realizada en BUYVAL";
	$Text1="Venta realizada";
	$Text2="Estos son los articulos vendidos en Buyval";
	$Textbutton="";
	$URLButton = "https://www.buyval.com/";	
	new_send_mail($Asunto, $Email, $ImagenPpal, $Text1, $Text2, $Text3, $Textbutton, $URLButton);
	}
	$ImagenPpal = "";
	$Asunto="Compra realizada";
	$Text1="Compra realizada";
	$Text2="Estos son tus articulos comprados en Buyval";
	$Textbutton="";
	$URLButton = "https://www.buyval.com/";	
	new_send_mail($Asunto, $Email, $ImagenPpal, $Text1, $Text2, $Text3, $Textbutton, $URLButton);
	
	
}
else {
$mysqli_conn->query("UPDATE orders SET Status = 'REJ' WHERE Id_order = '$Ref_venta'");
$mysqli_conn->query("UPDATE transaction SET Estado = 'REJECTED' WHERE Id_order = '$Ref_venta'");
}

}
?>

