<?php
	session_start();
	
	
	
	
	
	
	
	
	function emailPattern($email) {
			preg_match('/^(?:\w|\d)+@guhsd\.net$/', $email, $matches); //use regex pattern to test for match in given string (anything@guhsd.net)
			return count($matches) == 1 ? false : true; //if there's a match return false
	}
	function usernamePattern($username) {
		preg_match('/^(?:\w|\d|_){4,20}$/', $username, $matches); //use regex pattern to test for match in given string (alpanumeric and underscores [4-20]length)
		return count($matches) > 0 ? false : true; //if there's a match return false
	}
	function passwordPattern($password) {
		preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/', $password, $matches); //use regex pattern to test for match in given string (1 uppercase, 1 lowercase, 1 number, >= 8 characters)
		return count($matches) > 0 ? false : true; //if there's a match return false 	IF WE GUCCI ITS FALSE
	}
	function bioPattern($bio) {
		preg_match('/^(\w| |"|'."'".'|\?|\.|,|-|#|!|$|&|\*|@|%){0,150}$/', $bio, $matches); //use regex pattern to check if the bio is between 0 and 150 characters long and counts all of the characters specified in the regex as a character. If the character is not stated, it will throw an error
		return count($matches) > 0 ? false : true; //if there's a match return false
	}
	function picPattern($pic) {
		preg_match('/^([^()<>^\[\]{}*]){0,150}$/', $pic, $matches); //use regex pattern to check if the bio is between 0 and 150 characters long and counts all of the characters specified in the regex as a character. If the character is not stated, it will throw an error
		return count($matches) > 0 ? false : true; //if there's a match return false
	}
	function privacyPattern($privacy) {
		preg_match('/^public|private$/', $privacy, $matches); //If it's public or private
		return count($matches) > 0 ? false : true; //if there's a match return false
	}
	//Checks to see if the email already exist in the database
	function emailExist($email) {
		$pdo = DatabaseUser::connect();
		$sql = "SELECT * FROM users WHERE email LIKE '$email'";
		foreach($pdo->query($sql) as $row) {
			$return = $row['email'];
		}
		DatabaseUser::disconnect();
		
		//Return false if the email does not exist in the database
		$exist = ($return == '') ? false : true;
		return $exist || emailPattern($email);
	}
	
	//Checks to see if the username exist in database
	function usernameExists($username) {
		$pdo = DatabaseUser::connect();
		$sql = "SELECT * FROM users WHERE username LIKE '$username'";
		foreach($pdo->query($sql) as $row) {
			$return = $row['username'];
		}
		DatabaseUser::disconnect();
		
		//Returns false if the username does not exist in the database
		$exist = ($return == '') ? ($username == '0' ? true : false) : true;
		return $exist || usernamePattern($username);
	}
	
	//Checks to see if emailcode is in database already
	function emailCodeExist($emailCode) {
		$pdo = DatabaseUser::connect();
		$sql = "SELECT * FROM users WHERE emailCode LIKE '$emailCode'";
		foreach($pdo->query($sql) as $row) {
			$return = $row['emailCode'];
		}
		DatabaseUser::disconnect();
		
		//Return false if the emailcode does not exist in the database
		if ($return == ''){
			//echo 'doesnt exist';
			return false;
		}else{
			//echo 'exist';
			return true;
		}
	}
	
	//This function makes a string that is 40 characters long for the emailcode
	function randomCode(){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$result = '';
		for ($i = 0; $i < 40; $i++)
			$result .= $characters[mt_rand(0, 61)];
		
		$emailCode = $result;
		return $emailCode;
	}







	
	
	
	class User {
		
		
		
		
		
		
		
		
		
		public function login($post) {
			//$post = json_decode($p);
			$username = $post['username'];
			$password = $post['password'];
			//echo password_hash('pass1', PASSWORD_DEFAULT);
			
			$pdo = DatabaseUser::connect();
			$sql = "SELECT * FROM users WHERE username LIKE '$username'";
			foreach($pdo->query($sql) as $row) {
				$hashDB = $row['password'];
				//print_r($row);
			}
			DatabaseUser::disconnect();
			
			if (password_verify($password, $hashDB)) {
				$_SESSION['snapshot_username'] = $username;
				return 'true';
			}
			else {
				return 'false';
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		public function makeUser($post){
			$response = new Response();
			
			
			
			//Static variables to test to see if a function works
			/*$username = 'testUsername';//$_POST['username'];
			$fName = 'testFName';//$_POST['fName'];
			$lName = 'testLName';//$_POST['lName'];
			$email = '326088@guhsd.net';//$_POST['email'];
			$password = 'testPassword1';//$_POST['password'];*/
			
			//Dynamic varibles that will be recieved by the user to be put in the database
			$username = $post['username'];
			$fName = $post['fName'];
			$lName = $post['lName'];
			$email = $post['email'];
			$password = $post['password'];
			
			//Checks to see if any of these variables are blank. If one of them is, it'll throw a blank error at the user and not run anything else.
			if($username == "" ||$password == "" ||$email == "" ||$fName == "" ||$lName == "" ) {
				$response->addResponse('blank', true);
			}else {
				//Gets randomcode for the user
				$emailCode = randomCode();
				
				//Automaticly set to false until the user has varified their account
				$verified = 'false';
				$privacy = 'public';
				
				//Calls the functions defined earlier to check and see if the input already exist in the database
				$checkUsername = usernameExists($username);
				$checkEmail = emailExist($email);
				$checkEmailCode = emailCodeExist($emailCode);
				$checkPassword = passwordPattern($password);
				
				//If the password fits the requirment, it will then hash it. The password should return false and then this says if(not false), making it true to run
				if(!passwordPattern($password)) {
					$password = password_hash($password, PASSWORD_DEFAULT);
				}
				
				//Throws an error at the user if any of these are the error
				$response->addResponse('password', $checkPassword);
				$response->addResponse("username",$checkUsername);
				$response->addResponse("email",$checkEmail);
				
				//Checks to see if the email code exist and if so it will continue to run in the while statment until it gets a randomcode that isn't in the database
				if($checkEmailCode){
					while($checkEmailCode){
						$random = randomCode();
						$checkEmailCode = emailCodeExist($random);
					}
					$emailCode = $random;
				}
				
				//Boolean to check the three variables defined above
				$validityCheck = $checkEmail || $checkUsername || $checkPassword;
				
				//If all of it checks out, it will then be put into the database
			
				if(!$validityCheck) {
					$pdo = DatabaseUser::connect();
					$sql = 'INSERT INTO users (username, fName, lName, email, password, emailCode, verified, privacy, friendsList) values(?,?,?,?,?,?,?,?,?)';
					$q = $pdo->prepare($sql);
					$q->execute(array($username,$fName, $lName, $email, $password, $emailCode, $verified, $privacy, "[]"));
					$sql = "SELECT * FROM users WHERE username LIKE '$username'";
					foreach($pdo->query($sql) as $row) {
						$hashDB = $row['id'];
					}
					DatabaseUser::disconnect();
					$_SESSION['snapshot_username'] = $username;
					mail($email,"Verify your Snapshot account","Click the link below to verify your account: \n https://foothillertech.com/socialnetwork1/verifyUser.php?code=$emailCode");
				}
			
			}
			return $response->getResponse();
			
			//echo 'true' or 'false'
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		//Checks to see if the user is logged in
		public function checkUser($post) {
			$r = new Response();
			$bool = isset($_SESSION['snapshot_username']) ? false : true;
		  $response->addResponse('login', $bool);
		  $response->addData('["username":"'.$_SESSION['snapshot_username'].'"]');
		}
		
		
		
		
		
		
		
		
		
		
		
		
		//Checks to see if the user has verified their account
		public function verifyUser($post) {
			$code = $post['code'];
			$exists = emailCodeExist($code);//Checks to see if the email code sent already exists in the database
			//if doesnt exist return false
			if ($exists) {
				$pdo = DatabaseUser::connect();
				$sql = "UPDATE users SET verified=?, emailCode=? WHERE emailCode=?";
				$q= $pdo->prepare($sql);
				$q->execute(array("true", "", $code));
				return "true";
			}
			else {
				return "false";
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		//Resends the email if the user didn't get the email to verify their account
		public function resendEmail($post) {
			if(isset($_SESSION['snapshot_username'])) {
				$response = new Response();
				$username = $_SESSION['snapshot_username'];
				$emailCode = randomCode();
				$checkEmailCode = emailCodeExist($emailCode);//Checks if the email code is already used in the database
				//If the email code already exist, it will continue to make a random code until it does not exist in the database
				if($checkEmailCode){
					while($checkEmailCode){
						$random = randomCode();
						$checkEmailCode = emailCodeExist($random);
					}
					$emailCode = $random;
				}
			 	$pdo = DatabaseUser::connect();
				$sql = "SELECT * FROM users WHERE username LIKE '$username'";
				foreach($pdo->query($sql) as $row) {
					$res = $row;
				}
				$email = $res['email'];
				DatabaseUser::disconnect();
				$pdo = DatabaseUser::connect();
				$sql = "UPDATE users SET emailCode=? WHERE username LIKE '$username'";
				$q= $pdo->prepare($sql);
				$q->execute(array($emailCode));
				mail($email,"Verify your Snapshot account","Click the link below to verify your account: \n https://foothillertech.com/socialnetwork1/verifyUser.php?code=$emailCode");
				return $response->getResponse();
			}
			else {
				$response = new Response();
				$response->addResponse('login', true);
				return $response->getResponse();
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//Allows the user to look at another persons account, such as their name, username, bio, and picture
		public function viewUser($post){
			$response = new Response();
			$username = isset($post['username']) ? $post['username'] : '';
			if($username == ''){
				$response->addResponse('username', true);
				return $response->getResponse();
			}
			if($username == '0') {
				$username  = $_SESSION['snapshot_username'];
			}
			$response->addResponse('username', false);
			
			$pdo = DatabaseUser::connect();
			$sql = "SELECT * FROM users WHERE username LIKE '$username'";
			foreach($pdo->query($sql) as $row) {
				$res = $row;
			}
			DatabaseUser::disconnect();
			
			//Unsets what is stated below so another account can not view that
			unset($res{"password"});
			unset($res{"5"});
			unset($res{"selector"});
			unset($res{"9"});
			unset($res{"authenticator"});
			unset($res{"10"});
			unset($res{"emailCode"});
			unset($res{"11"});
			
			//Checks to see if the account is private or public
			$privacy = $res['privacy'];
			$currentUser = $res['username'];
			if ($privacy == 'public') {
				$json = json_encode($res);
				$response->addData($json);
			}
			
			elseif ($privacy == 'private') {
				$friends = $res['friendsList'];
				if ($username == $currentUser) {
						$json = json_encode($res);
						$response->addData($json);
				}
			else {
				if ($friends == '') {
					$json = json_encode("[]");
					$response->addData($json);
					$response->addResponse('private', true);
				}
				else {
					$friends = json_decode($friends);
					if (in_array($username, $friends)) {
							$json = json_encode($res);
							$response->addData($json);
					}
					else {
						$json = json_encode("[]");
						$response->addData($json);
						$response->addResponse('private', true);
					}
				}
			}
			}
			else {
					$json = json_encode("[]");
					$response->addData($json);
					$response->addResponse('private', true);
			}
			
			
			return $response->getResponse();
		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	//Allows the user to edit their account. They can only edit their username, password, bio, profile pic, and privacy
	public function editUser($post){
		$response = new Response();
		
		
		
		//Static variables to test to test if a function works
		/*$username = 'testUsername';//$_POST['username'];
		$password = 'testPassword1';//$_POST['password'];*/
		
		//Dynamic varibles that will be recieved by the user to be put in the database

		$fields = $post['fields'];//An array that detects what field the user is trying to edit ex: '["field1","field2"]'
		$values = $post['values'];//An array that will be the new value that will overwrite the values in the database already
	
	
		// $fields = '["bio","password"]';//,"email","gibberish"]';
		// $values = '["hello i am jeff", "passWord123"]';//, "newemail", "gibby"]';
		
		$fieldsObj = json_decode($fields);
		$valuesObj = json_decode($values);
		
		$editable = ['username','password','bio','profilePic','privacy'];//Fields that are able to be edited and put into the database
		$rm = [];
		
		//
		foreach ($fieldsObj as &$field) {
			if(!in_array($field, $editable)) {
				$i = array_search($field, $fieldsObj);
				array_push($rm, $i);
			}
		}
		
		unset($field);
		
		foreach($rm as &$ind) {
			unset($fieldsObj[$ind]);
			unset($valuesObj[$ind]);
		}
		
		unset($ind);
		
		$rm = [];
		//Goes through each of the array items the user is trying to edit. If any of the field object fails, it should throw an error to the user
		for($i = 0; $i < sizeof($fieldsObj); $i++) {
			$field = $fieldsObj[$i];
			$value = $valuesObj[$i];
			if ($field == 'username') {
				$checkUsername = usernameExists($value);
				if ($checkUsername) {
					array_push($rm, $i);
				}
				// else {
				// 	$_SESSION['snapshot_username'] = $value;
				// }
				$response->addResponse("username",$checkUsername);
			}
			elseif ($field == 'bio') {
				$checkBio = bioPattern($value);	
				if ($checkBio) {
					array_push($rm, $i);
				}
				$response->addResponse('bio', $checkBio);
			}
			elseif ($field == 'password') {
				$checkPassword = passwordPattern($value);
				if ($checkPassword) {
					array_push($rm, $i);
				}
				else {
					$valuesObj[$i] = password_hash($value, PASSWORD_DEFAULT);
				}
				$response->addResponse('password', $checkPassword);
			}
			elseif ($field == 'profilePic') {
				$checkPic = picPattern($value);
				if ($checkPic) {
					array_push($rm, $i);
				}
				$response->addResponse('profilePic', $checkPic);
			}
			elseif ($field == 'privacy') {
				$checkPrivacy = privacyPattern($value);
				if ($checkPrivacy) {
					array_push($rm, $i);
				}
				$response->addResponse('privacy', $checkPrivacy);
			}
		}
		
		$resp = json_decode($response->getResponse());
		 if (isset($_SESSION['snapshot_username'])) {
 			$username = $_SESSION['snapshot_username'];
		 	$pdo = DatabaseUser::connect();
			$sql = "SELECT * FROM users WHERE username LIKE '$username'";
			foreach($pdo->query($sql) as $row) {
				$res = $row;
			}
			DatabaseUser::disconnect();
			$verified = $res['verified'] == 'true' ? true : false;
			if($verified) {
				if ($resp->{"status"} == "true") {
					$pdo = DatabaseUser::connect();
					$str = "";
					$search = array_search('username', $fieldsObj);
					if ( $search !== false) {
						$_SESSION['snapshot_username'] = $valuesObj[$search];
					}
					foreach($fieldsObj as &$field) {
						$str .= "$field=?, ";
					}
					$str = substr($str, 0, strlen($str) - 2);
					$sql = "UPDATE users SET $str WHERE username LIKE '$username'";
					$q= $pdo->prepare($sql);
					$q->execute($valuesObj);
					DatabaseUser::disconnect();
				}
			}
			else {
				$r = new Response();
				$r->addResponse('verified', true);
				return $r->getResponse();
			}
			return $response->getResponse();
		}
		else {
					$r = new Response();
					$r->addResponse('login', true);
					return $r->getResponse();
		}
		
		
		
		
		
		
		
	
	}
	
	
	
	
	
	//Allows the user to add friends
		public function addFriend($post){
 			$response = new Response();
 			 $username = $post['username'];
 			$pdo = DatabaseUser::connect();
					$sql = "SELECT * FROM users WHERE username LIKE '$username'";
					foreach($pdo->query($sql) as $row) {
						$res = $row;
					}
					DatabaseUser::disconnect();

					$exists = $res == '' ? false : true;
				if ($exists) {
							if (isset($_SESSION['snapshot_username'])) {
							$user = $_SESSION['snapshot_username'];
							//$user = 'HydroDuck';
							$pdo = DatabaseUser::connect();
							$sql = "SELECT * FROM users WHERE username LIKE '$user'";
							foreach($pdo->query($sql) as $row) {
								$res = $row;
							}
													DatabaseUser::disconnect();

							//echo $res['friendsList'];
							$friends = $res['friendsList'];
							if ($friends == "") {
								$friendsArr = [$username];
								$friends = json_encode($friendsArr);
							}
							else {
								$friendsArr = json_decode($friends);
								array_push($friendsArr, $username);
								$friends = json_encode($friendsArr);
							}
							$pdo = DatabaseUser::connect();
							$sql = "UPDATE users SET friendsList=? WHERE username LIKE '$user'";
							$q= $pdo->prepare($sql);
							$q->execute(array($friends));
					DatabaseUser::disconnect();
					}
					else {
												$response->addResponse('login', true);
					
					}

					}
					else {
						$response->addResponse('exist', true);
					}

					
					
		return $response->getResponse();

			
	}
	
	
	
	
	
	
	
	
	
	

	public function logoutUser($post) {
		if (isset($_SESSION['snapshot_username'])) {
			unset($_SESSION['snapshot_username']);
			return "true";
		}
		else {
			return "false";
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	}
	
	
	
	
?>