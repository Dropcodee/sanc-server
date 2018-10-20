<?php
use Slim\Http\Request;
use Slim\Http\Response;
// Create and configure Slim app
$app = new \Slim\App;

/*************************************************************
 ******************** Get All Users **************************
 *************************************************************/
$app->get('/users', function($request, $response, $args) {
	// SQL Query to the database
	$sql = "SELECT * FROM users ORDER BY rand() LIMIT 5";
	
	// Get Db Object
	$db = new Db();
	
	// Connect and Send Query
	$users = $db->getQuery($sql);
	
	// Encode in JSON
	echo json_encode($users);
	
});

/*************************************************************
 ******************** Get a single user **********************
 *************************************************************/
$app->get('/users/{reg_no}', function($request, $response, $args) {
	$reg_no = $args['reg_no'];
	// SQL Query to the database
	$sql = "SELECT * FROM users WHERE reg_no = :reg_no || user_id = :user_id";

	// Assign the Parameters
	$db_params = [
		'reg_no' => $reg_no, 
		'user_id' => $reg_no
	];

	// Get the Db Object
	$db = new Db();

	// Connect and Send Query
	$user = $db->getQuery($sql, $db_params);

	// Get the Query in JSON Formate
	echo json_encode($user);
});

/*************************************************************
 ******************** Signup a Sanctuary User *********************
 *************************************************************/
$app->post('/add', function($request, $response, $args) {
	$user_id = "1234567890";
	$name = $request->getParam('name');
	$reg_no = $request->getParam('reg_no');
	$webmail = $request->getParam('webmail');
	$level = $request->getParam('level');
	$dob = $request->getParam('dob');
	$room = $request->getParam('room');
	$hall = $request->getParam('hall');
	$sex = $request->getParam('sex');
	$reasons = $request->getParam('reasons');
	$membership = $request->getParam('membership');
	$password = $request->getParam('password');

	$sql = "INSERT INTO users (user_id, name, webmail, reg_no, level, dob, hall, room, sex, reasons, membership, password) VALUES (:user_id, :name, :webmail, :reg_no, :level, :dob, :hall, :room, :sex, :reasons, :membership, :password)";
	$db_params = [
		'user_id' => password_hash($user_id, PASSWORD_DEFAULT),
		'name' => $name,
		'password' => password_hash($password, PASSWORD_BCRYPT, [12]),
		'webmail' => $webmail,
		'level' => $level,
		'dob' => $dob,
		'reg_no' => $reg_no,
		'room' => $room,
		'hall' => $hall,
		'sex' => $sex,
		'reasons' => $reasons,
		'membership' => $membership,
	];
	$db = new Db();
	$add = $db->postQuery($sql, $db_params, $msg = ''.$name.' has been added successfully');
});

/*************************************************************
 ******************** Login a Sanctuary User **********************
 *************************************************************/
$app->post('/login', function($request, $response, $args) {
	$reg_no = $request->getParam('reg_no');
	$password = $request->getParam('password');
	$db = new Db();
	$db = $db->connect();
	$statement = $db->prepare("SELECT password, user_id FROM users WHERE reg_no = :reg_no");
	$statement->execute(['reg_no' => $reg_no]);
	$user = $statement->fetch(PDO::FETCH_OBJ);
	$count = $statement->rowCount();

	if($count > 0) {

		$db_password = $user->password;

		if (password_verify($password, $db_password)) {
			session_start();
			$_SESSION['user_id'] = $user->user_id;
			$id = $user->user_id;
			$sql = "SELECT * FROM users";
			$db_param = ['user_id' => $id];
			$db = new Db();
			$db = $db->getQuery($sql, $db_param);
			echo '{"success": {"text": "Logged In"}}';
			// echo json_encode($db);
		} else {
			echo '{"error": {"text": "Incorrect registration number or password"}}';
		}

	} else {
		echo '{"error": {"text": "Incorrect registration number"}}';
	}
	
});