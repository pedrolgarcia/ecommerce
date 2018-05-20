<?php

	use \Hcode\Page;
	use \Hcode\Model\Category;
	use \Hcode\Model\Product;
	use \Hcode\Model\Cart;
	use \Hcode\Model\Address;
	use \Hcode\Model\User;
	use \Hcode\Model\Order;
	use \Hcode\Model\OrderStatus;

	$app->get('/', function() {
		$products = Product::listAll();
		$page = new Page();
		$page->setTpl("index", array(
			"products"=>Product::checkList($products)
		));
	});
	
	$app->get("/categories/:idcategory", function($idcategory) {
		$page = (isset($_GET["page"])) ? (int)$_GET["page"]	: 1;

		$category = new Category();	
		$category->get((int)$idcategory);

		$pagination = $category->getProductsPage($page);
		$pages = array();

		for ($i=1; $i <= $pagination["pages"]; $i++) { 
			array_push($pages, array(
				"link"=>"/categories/".$category->getidcategory()."?page=".$i,
				"page"=>$i
			));
		}

		$page = new Page();
		$page->setTpl("category", array(
			"category"=>$category->getValues(),
			"products"=>$pagination["data"],
			"pages"=>$pages
		));
	});

	$app->get("/products/:desurl", function($desurl) {
		$product = new Product();
		$product->getFromUrl($desurl);

		$page = new Page();
		$page->setTpl("product-detail", array(
			"product"=>$product->getValues(),
			"categories"=>$product->getCategories()
		));
	});

	$app->get("/login", function() {
		$page = new Page();
		$page->setTpl("login", array(
			"error"=>User::getError(),
			"errorRegister"=>User::getErrorRegister(),
			"registerValues"=>(isset($_SESSION["registerValues"])) ? $_SESSION["registerValues"] : array("name"=>"", "email"=>"", "phone"=>"")														
		));
	});

	$app->post("/login", function() {
		try
		{
			User::login($_POST["login"], $_POST["password"]);	
		} catch (Exception $e)
		{
			User::setError($e->getMessage());
		}
		
		header("Location: /checkout");
		exit;
	});

	$app->get("/logout", function() {
		User::logout();
		
		header("Location: /login");
		exit;
	});

	$app->post("/register", function() {
		$_SESSION["registerValues"] = $_POST;

		if(!isset($_POST["name"]) || $_POST["name"] == "") 
		{
			User::setErrorRegister("Preencha o seu nome.");
			header("Location: /login");
			exit;
		}
		if(!isset($_POST["email"]) || $_POST["email"] == "") 
		{
			User::setErrorRegister("Preencha o seu e-mail.");
			header("Location: /login");
			exit;
		}
		if(!isset($_POST["password"]) || $_POST["password"] == "") 
		{
			User::setErrorRegister("Preencha a sua senha.");
			header("Location: /login");
			exit;
		}
		if(User::checkLoginExist($_POST["email"])) 
		{
			User::setErrorRegister("Este endereço de e-mail já está senha usado	por outro usuário.");
			header("Location: /login");
			exit;
		}

		$user = new User();
		$user->setData(array(
			"inadmin"=>0,
			"deslogin"=>$_POST["email"],
			"desperson"=>$_POST["name"],
			"desemail"=>$_POST["email"],
			"despassword"=>$_POST["password"],
			"nrphone"=>$_POST["phone"]
		));
		$user->save();

		User::login($_POST["email"], $_POST["password"]);
		
		header("Location: /checkout");
		exit;
	});

	$app->get("/forgot", function() {
		$page = new Page();
		$page->setTpl("forgot");

	});

	$app->post("/forgot", function() {
		$user = User::getForgot($_POST["email"], false);
		
		header("Location: /forgot/sent");
		exit;
		
	});

	$app->get("/forgot/sent", function() {
		$page = new Page();
		$page->setTpl("forgot-sent");
		
	});

	$app->get("/forgot/reset", function() {
		$user = User::validForgotDecrypt($_GET["code"]);

		$page = new Page();
		$page->setTpl("forgot-reset", array(
			"name"=>$user["desperson"],
			"code"=>$_GET["code"]
		));
		
	});

	$app->post("/forgot/reset", function() {
		$forgot = User::validForgotDecrypt($_POST["code"]);
		User::setForgotUsed($forgot["idrecovery"]);

		$user = new User();
		$user->get((int)$forgot["iduser"]);
		$password = password_hash($_POST["password"], PASSWORD_DEFAULT, array("cost"=>12));
		$user->setPassword($password);

		$page = new Page();
		$page->setTpl("forgot-reset-success");
		
	});

	$app->get("/profile/orders", function() {
		User::verifyLogin(false);
		$user = User::getFromSession();

		$page = new Page();
		$page->setTpl("profile-orders", array(
			"orders"=>$user->getOrders()
		));
	});

?>