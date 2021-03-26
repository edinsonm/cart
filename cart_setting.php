<?php include('connections/dbsel.php'); ?>
<?php include('connections/comun.php'); 
session_start(); //start session 

if(isset($_POST["code"]))
{
    $statement = $mysqli_conn->prepare("SELECT Nombre, Precio FROM products WHERE Id_product=? LIMIT 1");
    $statement->bind_param('s', $_POST["code"]);
    $statement->execute();
    $statement->bind_result($name, $price);
    

    while($statement->fetch()){
        $new_product["nombre"] = $name; //fetch product name from database
        $new_product["precio"] = $price;  //fetch product price from database
    }
	$total = 0;
	$total = ($price * $_POST["addqty"]);
		
	echo 'Agregar ($'.$total.')';
	//echo $total;
}

if(isset($_SESSION["products"]) && count($_SESSION["products"])>0 && isset($_GET["bag"]) && ($_GET["bag"]==1)){ //if we have session variable
		//print_r ($_SESSION["products"]);
	
		$cart_box = '<div class="dropdown cart-dropdown"><a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static"><i class="minicart-icon"></i><span class="cart-count">'.$_SESSION["numitems"].'</span></a><div class="dropdown-menu" ><div class="dropdownmenu-wrapper">';
		$cart_box .= '<div class="dropdown-cart-header"><span>'.$_SESSION["numitems"].' Artículos</span><a href="cart">Ver carrito</a></div><!-- End .dropdown-cart-header --><div class="dropdown-cart-products">';
		//'<a href="#" data-toggle="dropdown" class="cart_bt"><i class="icon_bag_alt"></i><strong>'.$_SESSION["numitems"].'</strong></a><ul class="dropdown-menu" id="cart_items">';
		$total = 0;
        foreach($_SESSION["products"] as $product){ //loop though items and prepare html content
            //set variables to use them in HTML content below
            $nombre = $product["nombre"]; 
            $precio = $product["precio"];
			$imagen = $product["imagen"];
            $Id_product = $product["Id_product"];
			$prod_qty = $product["prod_qty"];
			$Id_allied = $product["allied_id"];
            
			$cart_box .= '<div class="product"><div class="product-details"><h4 class="product-title"><a href="product?ID='.$Id_product.'">'.$nombre.'</a></h4>';
			$cart_box .= '<span class="cart-product-info"><span class="cart-product-qty">'.$prod_qty.'</span>x $'.number_format($precio, 0, ",", ".").'</span></div><!-- End .product-details -->';
			$cart_box .= '<figure class="product-image-container"><a href="product?ID='.$Id_product.'" class="product-image"><img src="img/allies/'.$Id_allied.'/catalog/'.$imagen.'" alt="product" width="80" height="80">';
            $cart_box .= '</a><a href="#" class="btn-remove" title="Eliminar producto" data-code="'.$Id_product.'"><i class="icon-retweet"></i></a></figure></div><!-- End .product -->';
		
		$subtotal = ($precio * $prod_qty);
		$total = ($total + $subtotal);
		$_SESSION["total"] = $total;
		}
		
		$cart_box .= '<div class="dropdown-cart-total"><span>Total</span><span class="cart-total-price">$'.number_format($_SESSION["total"], 0, ",", ".").'</span></div><!-- End .dropdown-cart-total -->';
		$cart_box .= '<div class="dropdown-cart-action"><a href="cart" class="btn btn-block">COMPRAR</a></div><!-- End .dropdown-cart-total -->';
		
        echo ($cart_box); //exit and output content
		//echo "hola";
}
//else echo '<a href="#" data-toggle="dropdown" class="cart_bt"><i class="icon_bag_alt"></i><strong>0</strong></a>';
if(isset($_POST["productId"]))
{
    foreach($_POST as $key => $value){
        $new_product[$key] = filter_var($value, FILTER_SANITIZE_STRING); //create a new product array 
    }
    $ID = $_POST["productId"];
    //we need to get product name and price from database.
    $result = $mysqli_conn->query("SELECT Nombre, Precio FROM products WHERE Id_product='$ID'");
	$row = mysqli_fetch_assoc($result);
	$name = $row["Nombre"];
	$price = $row["Precio"];  

    //while($statement->fetch()){
        $new_product["nombre"] = $name; //fetch product name from database
        $new_product["precio"] = $price;  //fetch product price from database
		$new_product["Id_product"] = $ID;
		$new_product["imagen"] = $_POST["imagen"];
		$new_product["prod_qty"] = $_POST["addquantity"];
		$new_product["color"] = $_POST["refcolor"];
		$new_product["allied_id"] = $_POST["allied_id"];
        
        if(isset($_SESSION["products"])){  //if session var already exist
            if(isset($_SESSION["products"][$new_product['Id_product']])) //check item exist in products array
            {
                unset($_SESSION["products"][$new_product['Id_product']]); //unset old item
            }           
        }  
        $_SESSION["products"][$new_product['Id_product']] = $new_product; //update products with new item array   
    //}
    //die(json_encode(array('items'=>$total_items))); //output json 
}

if(isset($_GET["add_box"]))
{
    $ID = $_GET["add_box"];
    //we need to get product name and price from database.
    $result = $mysqli_conn->query("SELECT p.*, pm.Source FROM products p, products_img pm WHERE p.Id_product=pm.Id_product AND p.Id_product='$ID' AND pm.Main=1");

	//echo "SELECT p.*, pm.Source FROM products p, products_img pm WHERE p.Id_product=pm.Id_product AND Id_product='$ID' AND pm.Main=1";
	$row = mysqli_fetch_assoc($result);
	$name = $row["Nombre"];
	$price = $row["Precio"];  
	$imagen = $row["Source"];
	$Id_allied = $row["Id_allied"];
	$prod_qty = 1;
	$refcolor = "";

    //while($statement->fetch()){
        $new_product["nombre"] = $name; //fetch product name from database
        $new_product["precio"] = $price;  //fetch product price from database
		$new_product["Id_product"] = $ID;
		$new_product["imagen"] = $imagen;
		$new_product["prod_qty"] = $prod_qty;
		$new_product["color"] = $refcolor;
		$new_product["allied_id"] = $Id_allied;
        
		//print_r($new_product);
		
        if(isset($_SESSION["products"])){  //if session var already exist
            if(isset($_SESSION["products"][$new_product['Id_product']])) //check item exist in products array
            {
                unset($_SESSION["products"][$new_product['Id_product']]); //unset old item
            }           
        }  
        $_SESSION["products"][$new_product['Id_product']] = $new_product; //update products with new item array   
    //}
    //die(json_encode(array('items'=>$total_items))); //output json 
}

if(isset($_POST["addqty"]) && isset($_SESSION["products"]))
{       
//print_r($_SESSION["products"]);
        if(isset($_SESSION["products"])){  //if session var already exist
            if(isset($_SESSION["products"][$_POST["update_code"]])) //check item exist in products array
            {
				$new_product["Id_product"] = $_POST["update_code"];
				$new_product["prod_qty"] = $_POST["addqty"];
				$new_product["nombre"] = $_SESSION["products"][$_POST["update_code"]]['nombre'];//fetch product name from database
				$new_product["precio"] = $_SESSION["products"][$_POST["update_code"]]['precio'];  //fetch product price from database
				$new_product["imagen"] = $_SESSION["products"][$_POST["update_code"]]['imagen'];
				$new_product["color"] = $_SESSION["products"][$_POST["update_code"]]['color'];
				$new_product["allied_id"] = $_SESSION["products"][$_POST["update_code"]]['allied_id'];
                unset($_SESSION["products"][$new_product['Id_product']]); //unset old item
            }           
        }
        
        $_SESSION["products"][$new_product['Id_product']] = $new_product; //update products with new item array   
    
	$total = 0;
	$total = ($new_product["precio"] * $_POST["addqty"]);
		
	echo '$'.number_format($total, 0, ",", ".");
}

if(isset($_GET["total"]) && isset($_SESSION["products"]))
{
    echo '$'.number_format($_SESSION["total"], 0, ",", ".");
}

if(isset($_GET["remove_code"]) && isset($_SESSION["products"]))
{
    $Id_product = filter_var($_GET["remove_code"], FILTER_SANITIZE_STRING); //get the product code to remove

    if(isset($_SESSION["products"][$Id_product]))
    {
        unset($_SESSION["products"][$Id_product]);
		unset($_SESSION["total"]);
    }
	if($_SESSION["products"])
		echo 1;
}

if(isset($_GET["cancelar"]) && ($_GET["cancelar"]==1))
{
        unset($_SESSION["products"]);
		unset($_SESSION["total"]);
		unset($_SESSION["numitems"]);
}

 
if($_SESSION["products"])
$_SESSION["numitems"] = count($_SESSION["products"]); //count total items
else $_SESSION["numitems"] = 0;
?>