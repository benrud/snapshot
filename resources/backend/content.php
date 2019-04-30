<?php
	/*
	Content.php contains the back end class of the content posted in the social network....
	Content.php is composed of:
	*starting sessions and connections of the database
	+a class that defines what is content
		+a function that gets content
			*a function that gets content such as posts
		+a function that puts content
			*a function that puts content such as posts


	*/
	session_start();
	require_once("./content/connect.php"); //Includes database file
	$pdo = DatabaseContent::connect(); //Creates the class and then calls the connect function
	
		/*
		Example for Database handling:
			$id = '' ; //sets the variable that id should be
    	$sql = "SELECT * FROM posts WHERE id like "'.id.'" "; //returns all alike `$id`

    	$result = $conn->query($sql); //connects to database and executes query
    	echo $result; //outputs query's result
		
		
		*/
	class Content{ //This is the Content class
		
		/*
		To create a function, make sure you make it public and define it as a function then give it a name
		and you are not return anything
		the way you return is via the `echo` function
		for example
		
		public function helloWorld(){
			echo `hello world`
		}
		
		to call, make an HTTP request to foothillertech.com/socialnetwork2/resources/backend/content/helloWorld
		
		this should return the string `hello world`
		
		to make single HTTP requests we can use the terminal...
		Use the unix command `curl` with the first parameter being the url requesting
		For example:
		Open the terminal and type,
		curl foothillertech.com/socialnetwork2/resources/backend/content/getContent

		And the script should return the values or data in the database from the getContent class below



		*/
		
		
		
	//Getting content from database
	 	public function getContent(){
    //curl foothillertech.com/socialnetwork2/resources/backend/content/getContent
	 		
	 		$pdo = DatabaseContent::connect(); //Creates the DatabaseContent class and then calls the connect function
			$sql = "select * from `post` order by `date`"; //SQL statement that selects everything chronological
			$JSONparse = ''; //Creates a JSONparse
			// Defining arrays of data
			//$content = array();
			$i = 0;
			foreach($pdo->query($sql) as $row) {
						//Defining by database
						//Every function compiles the database the same way
						$username = ($row[0]); //This defines the 1st row as username
						$id = ($row[1]); //Same as id defining 2nd row
						$date = ($row[2]); //3rd row
						$image = ($row[3]); //4th row
						$caption = ($row[4]); //5th row
						$b = ($id != "2") ? "," : ""; //This is logic for JSON
						/*
							JSON has it so that every object has to be followed with a comma, exept for the last one. However, 
						*/
						$JSONparse = $JSONparse . $b . '"' . $i . '":{ "username": "' . $username . '","id": "' . $id . '", "date": "' . $date . '", "image": "' . $caption . '", "caption": "' . $image . '"}'; //Commonality for caption
						$i++;
					}
			$JSONobject =  '{' . $JSONparse . '}'; //Collects in brakets
			echo $JSONobject;
			DatabaseContent::disconnect();
	 	}
	 	//Filtering data from database
	 	public function getSinglePost($post){
	 		//curl -d "id=7" -X POST foothillertech.com/socialnetwork2/resources/backend/content/getSinglePost
	 		
	 		$pdo = DatabaseContent::connect();
	 	 	$sql = 'SELECT * FROM `post` WHERE `postid`= ' . $post['id'];
	 	 	$i = 0;
			foreach($pdo->query($sql) as $row) {
						//Defining by database
						$username = ($row[0]);
						$id = ($row[1]);
						$date = ($row[2]);
						$image = ($row[3]);
						$caption = ($row[4]);
						$b = ($i === 1) ? "," : "";
						$JSONparse = $JSONparse . $b . '"' . $i . '":{ "username": "' . $username . '","id": "' . $id . '", "date": "' . $date . '", "image": "' . $caption . '", "caption": "' . $image . '"}';
						$i++;
					}
			$JSONobject =  '{' . $JSONparse . '}';
			echo $JSONobject;
	 	 DatabaseContent::disconnect();
	 	
	 		
	 	}
	 	public function getUserPost($post){
	 		//curl -d "username='MatthewG'" -X POST foothillertech.com/socialnetwork2/resources/backend/content/getUserPost
	 		
	 		$pdo = DatabaseContent::connect();
	 	 	$sql = 'SELECT * FROM `post` WHERE `username`= ' . $post['username'] . ' DESC';
	 	 	$i = 0;
			foreach($pdo->query($sql) as $row) {
						//Defining by database
						$username = ($row[0]);
						$id = ($row[1]);
						$date = ($row[2]);
						$image = ($row[3]);
						$caption = ($row[4]);
						$b = ($i === 1) ? "," : "";
						$JSONparse = $JSONparse . $b . '"' . $i . '":{ "username": "' . $username . '","id": "' . $id . '", "date": "' . $date . '", "image": "' . $caption . '", "caption": "' . $image . '"}';
						$i++;
					}
			$JSONobject =  '{' . $JSONparse . '}'; //collects in brackets
			echo $JSONobject;
	 	 DatabaseContent::disconnect();
	 	
	 		
	 	}
	 	
	 	public function putUserPost($post){
	 		//print_r($post);
	 		//return;
	 		//curl -d "loggedIn=true&username='MatthewG'&caption='yeet works'&image='somestring.com'" -X POST foothillertech.com/socialnetwork2/resources/backend/content/putUserPost
	 		
			 
			//putUserPost() first checks if the user is logged in in the line below. 

			
	 		if(isset($_SESSION['snapshot_username'])){ 
	 			/*$pdo = DatabaseContent::connect();
	 			//$username = $_SESSION['snapshot_username'];
	 			$username = 'MatthewG';
	 			$sql = "INSERT INTO post(username,caption,image)
						VALUES(" . $username . ", " . $post['caption'] . ", " . $post['image'] . ")";
				
				$pdo->query($sql);
				*/
				$username = $_SESSION['snapshot_username']; //this defines the username
				$caption = $post['caption']; //This defines the caption
				$image = $post['image']; //This defines the URL
				$pdo = DatabaseContent::connect(); //
					$sql = 'INSERT INTO post (username, caption, image) values(?,?,?)';
					$q = $pdo->prepare($sql);
					$q->execute(array($username,$caption, $image));
				
				
				return $username . ' ; ' . $caption . ' ; ' . $image;
	 			
	 		}else{
	 			return 'Need to Log In';
	 		}
	 		
	 	}
	 	
	}
	

?>