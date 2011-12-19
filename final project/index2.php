<?php
session_start();
$className = 'index';

if(!empty($_GET)) {
	
	//rint_r($_GET);
	arsort($_GET);
	
	$className = key($_GET);
	
	//print($className);
} 

$obj = new $className();

abstract class mongo_data {
	
	protected $db;
	protected $collection;
	protected $cursor;
	protected $record_id;
	protected $temp;
	protected $record;
	
	protected function mconnect() {
		$username = 'kwilliams';
		$password = 'mongo1234';
		
		//$m = new Mongo();
		//$db = $m->comedy;
		
		//$this->connection = new Mongo("mongodb://${username}:${password}@localhost/test",array("persist" => "x"));
		$this->connection = new Mongo();
		//$db = $m->comedy;
		
		$this->setDb();
	}
	protected function setDb($db = 'default1') {
		//$this->db = $this->connection->$db;
		
		$this->db = $db = $this->connection->test;
	}
	protected function setCollection($collection) {
		$this->collection = $this->db->$collection;
		
	}
	protected function findRecords($query = null) {
		if($query == null) {
			$this->cursor = $this->collection->find();
		} else {
			$this->cursor = $this->collection->find($query);
		}
		return $this->cursor;
	}
	
	protected function findRecord($query = null) {
		
		
		
		
		if($query == null) {
		
			$this->record = $this->collection->findOne();
		} else {
			
			$this->record = $this->collection->findOne($query);
		}
		
		//print_r($this->record);
		
		return $this->record;
	}
	
	protected function add($query) {
		$this->collection->insert($query);
		$this->record_id = $query;
		$this->cursor = $this->collection->find();
	}
	
	
	
	protected function update($query) {
		
		
		//$c->update(array("firstname" => "Bob"), $newdata);
		
		//print("record id:".$this->record['_id']);		
		//print_r($this->record);
		
		//$array = array("$oid" => $this->record_id)
		
		//MongoId Object ( [$id] => 4ee3e1c5c15a89d410000006 )
		$mongoID = new MongoID($_SESSION['id']);
		
		
		
		// echo "0 update func: ".$this->record_id.'<br>';
		// echo "1 update func: ".$_SESSION['id'].'<br>';
		// echo "2 update func: ".$mongoID.'<br>';
		
		
		$this->collection->update(array('_id'   => $_SESSION['id']),$query);
		
		
	}
	protected function delete($query) {
		
	}
		
	
	
	protected function getRecord() {
		foreach($this->record as $key => $value) {
				
				$this->temp .= $key . ': ' . $value . "<br>\n";
				
			}		
			$this->temp .= '<hr>';
		return $this->temp;
	}


	protected function getRecords() {
			
		$prim_key;
		
		
		
		
		
		foreach($this->cursor as $record) {
			
			$this->temp.='<table border = 1 align="center" cellpadding="3">';
			foreach($record as $key => $value) {
				$this->temp.='<tr>';
				if($key == '_id'){
					$prim_key = $value;
				}
			
				
				$this->temp.='<td>'.$key.'</td>';
				$this->temp.='<td>'.$value.'</td>';
				//$this->temp .= $key . ': ' . $value . "<br>\n";
				$this->temp.='</tr>';
			}		
			$this->temp.='<tr>';
			$this->temp.='<td colspan=2>';
			$this->temp .= '<a href="index2.php?people=delete&id='.$record['_id'].'">Delete</a>';
			$this->temp.='</td>';
			$this->temp.='</tr>';
			
			$this->temp.='</table><br>';
		}
		
		
		
		//print($prim_key);
		return $this->temp;
	}
 	protected function getRecordID() {
 		return $this->record_id;
 	}
}






abstract class data extends mongo_data {
	protected $query;
	protected $connection;
}
abstract class request extends data {
	protected $data;
	protected $form;
	 function __construct() {
	 	
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$this->get();

		} else {
			
			$this->post();
		}
		$this->display();
	}
	protected function get() {
		// gets the first value of the $_GET array, so that the correct form function is called.
		$function = array_shift($_GET) . '_get';
		$this->$function();
	}
	protected function post() {
		// gets the first value of the $_GET array, so that the correct form function is called.
		$function = array_shift($_GET) . '_post';
		$this->$function();
	}
}


//this is the class for the homepage

abstract class page extends request {
	protected $header;
	protected $content = '';
	
	protected $footer;
	
	protected function display() {
		echo $this->setHeader();
		
		// $className = 'index';
		// $className::get();
			
		echo $this->content;
		echo $this->setFooter();
	}

	protected function setHeader() {
		$this->header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
						 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
						 <html xmlns="http://www.w3.org/1999/xhtml">
						<head>
							

								   <style type="text/css">
    
								      body {
								        
								        color: black
								      }
								
									  
									  div.my_header{
	
	
										    width: 900px;
										    height: 100px;    																					
										    border: 2px solid pink;
											text-align:center;
											background: pink; 	
									  }
									  
									  div.leftColumn {
									  			float: left;
											    padding: 10px;
											    
											    width: 190px;
												height: 1075px;
									
											border: 2px solid pink;
									  }
									  
									  div.rightColumn {
									  		
											float: left;
									  		width: 500px;
											padding: 12px;
											
											text-align:center;
											
											
											border: 0px solid pink;
									  }
									  
									
									  
									  div.my_wrapper{
										    background: #F0F0F0;
											height: 1200px;	
											width: 900px;
											
											border: 3px solid pink;  	
										}
									  
								    
								    </style>



						</head>
						<body> <div class="my_wrapper"> <div class="my_header"> <h1>Welcome </h1></div><div class="leftColumn"><br><p>';
						
						if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']==TRUE){
			
							$this->header .= '<h4>Welcome '.$_SESSION['fname'].' '.$_SESSION['lname'].'</h4>  ';
							
							//$this->header .= '<a href="index2?people=logout">Not '.$_SESSION['fname'].'?</a><br><br>';
							$this->header .= '<a href="index2.php?people=logout">Logout</a><br><br>';
							
							$this->header .= '<a href="index2.php?people=user">View Your Account</a><br><br>';
						}
				
				
						if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']==TRUE){
								
						}else{
							$this->header .= '<a href="index2.php?people=signup">Signup</a><br><br>';
							$this->header .= '<a href="index2.php?people=login">Login</a><br><br>';
						}
						
						$this->header .= '<a href="index2.php?people=directory">View Existing Users</a><br></p></div><div class="rightColumn">';
		
		
		
						
						// div.rightColumn {position: absolute; top: 120px; right: 50px; width: 1030px}
		return $this->header;
	}

	protected function setFooter() {
		$this->footer = '</div></body>
					     </html>';
		return $this->footer;
	
	}


}



class index extends page {
	function __construct() {
		parent::__construct();
	}

	protected function get() {

		//$this->content = '<h1>Welcome To The App</h1>';
		
	
		
		//print_r($_SESSION);
		
		// if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']==TRUE){
// 			
			// $this->content .= '<br>Welcome '.$_SESSION['fname'].' '.$_SESSION['lname'].'<BR>  ';
// 			
			// $this->content .= '<a href="index2?people=logout">Not '.$_SESSION['fname'].'?</a><br><br>';
			// $this->content .= '<a href="index2?people=user">Click Here To View Your Account</a><br>';
		// }
// 
// 
		// if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']==TRUE){
// 				
		// }else{
			// $this->content .= '<a href="index2?people=signup">Click Here To Signup</a><br>';
			// $this->content .= '<a href="index2?people=login">Click Here To Login</a><br>';
		// }
// 		
		// $this->content .= '<a href="index2?people=directory">Click Here To View Users</a><br></div>';
		
		
		//session_destroy();
		//$this->content .= '<a href="index2?people=reset">Click Here To Reset Your Password</a><br>';
		//$this->content .= '<a href="index2?service=city">Click Here To List Cities</a><br>';	
	
	}
}
//this will handle logins

class people extends page {
	
	
	
	function __construct() {
		$this->mconnect();
		$this->setCollection('people');
		parent::__construct();
	}

	protected function login_get() {
		
		$this->content = '<BR><h2>Login </h2> <BR>';
		$this->content .= $this->login_form();
		$this->content .= '<BR>'.(isset($_SESSION['error'])?$_SESSION['error']:'');

	}
	
	protected function login_form() {
		
			$this->form = 
						'<FORM action="./index2.php?people=login" method="post">
						
	    				   <LABEL for="email">Email: </LABEL>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	              		   		<INPUT name="email" type="text" id="email"><BR>
	    		           <LABEL for="password">Password: </LABEL>&nbsp;&nbsp;
	                       		<INPUT name="password" type="password" id="password"><BR><BR>
	                       <INPUT type="submit" value="Login"> </br>
	                       		
	                       		
 					   </FORM>';
		return $this->form;
	
	}




	
	protected function login_post() {
		
		
		//echo "helloo:".$_POST['username'];
		//echo "helloo:".$_POST['password'];
		
		$this->findRecord(array('email' => $_POST['email'],'password' => $_POST['password']));
		
		
		//print_r($this->record);
		
		if($this->record == ''){
			
			$_SESSION['error'] = 'INVALID LOGGIN, TRY AGAIN';
			$this->login_get();
			
			
		}else{
			$_SESSION['error'] = '';
			//print_r($this->record['_id']);
			
			$_SESSION['id'] = $this->record['_id'];	
			$_SESSION['fname'] = $this->record['fname'];
			$_SESSION['lname'] = $this->record['lname'];
			$_SESSION['email'] = $this->record['email'];		
			$_SESSION['is_logged_in'] = TRUE;
			
			
			// $className = 'index';
			// $className::get();
		
			//$this->signup_get();			
		}
		
		//print_r($_SESSION);
		//echo $_SESSION['email'];
		
		
		
		
		//$this->content .= '<a href="index2?people=user">Click Here To View Your Account</a><br>';
	
	}





	protected function signup_get() {
		
		$this->content = '<h2>Register</h2>';
		$this->content .= $this->signup_form();
		
	}
	protected function signup_form() {
		
		//print_r($_SESSION);
		
		
		//<LABEL for="zip">Zip Code: </LABEL>
              		   		//<INPUT type="text" name="zip" id="zip"><BR>
		
		//print(' IN              ----------------SIGNUPFORM <BR>');
		
		
		if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in']==TRUE){
			
			
			$this->form = '<FORM action="./index2.php?people=update" method="post"> ';
		}else{
			
			$this->form = '<FORM action="./index2.php?people=signup" method="post"> ';
		}
		
		//<INPUT type="text" name="fname" id="firstname" value='.(isset($_SESSION['fname']) ? $_SESSION['fname'] : '').'><BR>
		
    	$this->form .= '<LABEL for="firstname">First name: </LABEL>
    				   
              		   		<INPUT type="text" name="fname" id="firstname" value='.(isset($this->record['fname']) ? $this->record['fname'] : '').'><BR>
              		   		
    				   <LABEL for="lastname">Last name: </LABEL>
    				   
              		   		<INPUT type="text" name="lname" id="lastname" value='.(isset($this->record['lname']) ? $this->record['lname'] : '').'><BR>
              		   		
    				   <LABEL for="email">Email: </LABEL>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    				   
              		   		<INPUT type="text" name="email" id="email" value='.(isset($this->record['email']) ? $this->record['email'] : '').'><BR>
              		   		
						<LABEL for="password">Password: </LABEL>
						
              		   		<INPUT type="text" name="password" id="email" value='.(isset($this->record['password']) ? $this->record['password'] : '').'><BR><BR>
              		   		
              		   							
							
              		   <INPUT type="submit" value="Save"> 
    				   </P>
				   	   </FORM>';
		return $this->form;			  
	}



	protected function signup_post() {
		$this->add($_POST);
		$this->getRecordID();
		
		// $className = 'index';
		// $className::get();
		
		//$this->content .= '<a href="index2?people=login">Click Here To Login</a><br>';
		//$this->content .= '<a href="index2?people=directory">Click Here To View Users</a><br>';
	}
	
	
	protected function update_post() {
		
		//echo "update_post";
		$this->update($_POST);
		
		
		// $className = 'index';
		// $className::get();
		
		//$this->getRecordID();
		//$this->content .= '<a href="index2?people=login">Click Here To Login</a><br>';
		//$this->content .= '<a href="index2?people=directory">Click Here To View Users</a><br>';
	}

	protected function delete_get() {
		
		
		//print("--IN DELETE---:".$_GET['id']);
		$this->collection->remove(array('_id' => new MongoId( $_GET['id'])), array("justOne" => true));
		$this->directory_get();
		
		
	}

	
	protected function directory_get() {
		$this->content = '<h2>Existing User Accounts</h2>';
		$this->findRecords();
		$this->content .= $this->getRecords();
	}

	protected function logout_get() {
		
		//print("LOGINGOUT");
		session_destroy();
		$_SESSION['is_logged_in'] = FALSE;
		//$this->index->get();
		
		
		$className = 'index';
		$className::get();
		
		//$this->content = get();
		
		//$this->get();
		//$this->content = '<h1>User Accounts</h1>';
		
	}
	
	
	
	
	
	
	
	
	
	
	
	protected function user_get() {
			
		//print('sdfsdf:'.$_SESSION['email'].'<BR>');
		
		
		$this->findRecord(array('email' => $_SESSION['email']));
		//$this->content = $this->getRecord();
		//print('SIGNUPFORM <BR>');
		
		$this->content .= '<h2>Personal Information</h2><br>';
		$this->content .= $this->signup_form();
	}







	
	protected function reset_get() {
	
		$this->content = '<h1>reset your password</h1>';
		$this->content .= $this->reset_form();
	}
	protected function reset_post() {
				
			print_r($this->findRecord(array('email' => $_POST['email'])));
	}
	protected function reset_form() {
		$this->form = '<FORM action="./index2.php?people=reset" method="post">
              		   <LABEL for="email">Email Address:</LABEL>
              		   <INPUT type="text" name="email" id="email"><BR>
    				   <INPUT type="submit" value="Send My Password">
    				   </P>
				   	   </FORM>';
		return $this->form;	

	}

}

class service extends page {
	function __construct() {
		$this->mconnect();
		$this->setCollection('states');
		parent::__construct();
	}
	
	protected function city_get() {
		
		$this->content = '<form action="index2.php?service=city" method="post">
  State: <input type="text" name="state" /><br />
  <input type="submit" value="Submit" />
</form>';
	
	}
	
	protected function city_post() {
		$this->add($_POST);
		$this->content .= $this->getRecords();
		
		
	
	}

}




?>