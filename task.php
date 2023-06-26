<?php

// Define content type json to fetch API HTTP request response
#header("Content-Type:application/json");
session_start();

// Database Class Buid Secure MySql connection with PHP
class Database{
    private $servername ;
    private $username;
    private $password;
    private $dbname;

    public function __construct()
    {
        $this->servername = "localhost";
        $this->username = "root";
        $this->password = "";
        $this->dbname = "taskmanager";
    }
    public function connect()
    {  
        $conn =  new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        } 
        else
        {
            return $conn;
        }
    }
}

// MySQL Operations For CRUD
class Operation extends Database{
  
  	public function selectAll($tableName)  
  	{
  		$conn = $this->connect();
  		$sql = "SELECT * FROM $tableName "; 
  		$result = $conn->query($sql); 
  		return $result;             
  	}

  	public function select_with_join($columns,$tableName1,$tableName2,$join,$onConditon)
  	{
       $conn = $this->connect();
       $sql = "SELECT $columns FROM $tableName1 $join $tableName2 ON  $onConditon"; // query for user  
       $result = $conn->query($sql);
       return $result;
  	}

    public function select_with_join_condition($columns,$tableName1,$tableName2,$join,$onConditon,$whereConditon)
    {
       $conn = $this->connect();
       $sql = "SELECT $columns FROM $tableName1 $join $tableName2 ON  $onConditon WHERE $whereConditon"; // query for user  
       $result = $conn->query($sql);
       return $result;
    }

    public function select_with_condition($columns,$tableName,$whereConditon)
    {
       $conn = $this->connect();
       $column = implode(',', $columns);
       $sql = "SELECT $column FROM $tableName WHERE $whereConditon";
       $result = $conn->query($sql);
       return $result;
    }

     public function select_with_multiple_condition($columns,$tableName,$whereConditon1,$operator,$whereConditon2)
    {
       $conn = $this->connect();
       $column = implode(',', $columns);  
       $sql = "SELECT $column FROM $tableName WHERE $whereConditon1 $operator $whereConditon2";
       $result = $conn->query($sql);
       return $result;
    }

     public function select_with_multiple_conditions($columns,$tableName,$whereConditon1,$operator,$whereConditon2,$whereConditon3)
    {
       $conn = $this->connect();
       $column = implode(',', $columns); 
        $sql = "SELECT $column FROM $tableName WHERE $whereConditon1 $operator $whereConditon2 $operator $whereConditon3";
       $result = $conn->query($sql);
       return $result;
    }

    public function select_with_condition_orderby($columns,$tableName,$whereConditon,$orderByColumn,$asc_decs)
    {
       $conn = $this->connect();
       $column = implode(',', $columns);
       $sql = "SELECT $column FROM $tableName WHERE $whereConditon ORDER BY $orderByColumn $asc_decs";
       $result = $conn->query($sql);
       return $result; 
    }

    public function select_with_multiple_condition_orderby($columns,$tableName,$whereConditon1,$operator,$whereConditon2,$orderByColumn,$asc_decs)
    {
       $conn = $this->connect();
       $column = implode(',', $columns);
       $sql = "SELECT $column FROM $tableName WHERE $whereConditon1 $operator $whereConditon2 ORDER BY $orderByColumn $asc_decs";
       $result = $conn->query($sql);
       return $result; 
    }

    public function update($tableName,$columns,$whereConditon)
    {
        $conn = $this->connect();
        $column = implode(',', $columns);
        $sql = "UPDATE $tableName set $column WHERE $whereConditon"; 
        $result = $conn->query($sql);
        return $result;
    } 

    public function insert($tableName,$columns,$columnsValues)
    {
        $conn = $this->connect();
        $column = implode(',', $columns);
        $columnsValue = implode(',', $columnsValues);
        $sql = "INSERT INTO $tableName ($column) VALUES ($columnsValue)";
        $result = $conn->query($sql);
        return $result;
    }

    public function delete($tableName,$whereConditon)
    {
        $conn = $this->connect();
        $sql = "DELETE from $tableName WHERE $whereConditon";
        $result = $conn->query($sql);
        return $result;
    }

    
}

// Encode MySql Object into JSON
function json_response($response){
    $json_response = json_encode($response);
	echo $json_response;
}

// Check Email Registered
function check_email_registered($email){
    $response=[];
    $func = new Operation();
    $row = $func->select_with_condition(array('*'),'user', "user_email = '".$email."'");
    if($row->num_rows > 0){
        return true;
    }else{
        return false;
    }
}

// POST Object Validator
function field_validator($fields, $post_data){
    $error_arr = [];
    $data_arr=[];
    $result = [];
    $raise_error = False;
    foreach($fields as $field){
        if(array_key_exists($field, $post_data) && $post_data[$field] != ""){
            $data_arr[$field] = trim($post_data[$field]);
        }else{
            $error_arr[$field] = "Required Field";
            $raise_error = True;
        }
    }
    if(!$raise_error){
        $result['error'] = $raise_error;
        $result['data'] = $data_arr;
    }else{
        $result['error'] = $raise_error;
        $result['data'] = $error_arr;
    }
    return $result;
}

// Upload Assets
function upload_assets($dir, $file){}

// List down all Users
function list_users(){
    $response_arr=[];
    $func = new Operation();
    $result = $func->selectAll('user'); 
    if(mysqli_num_rows($result)>0){
        $rows = mysqli_fetch_all($result);
        foreach ($rows as $row) {
            $response['id'] = trim($row['0']);
            $response['name'] =  trim($row['1']);
            array_push($response_arr, $response);
        }
        json_response($response_arr);
    }else{
        json_response(NULL, NULL, 200,"No Record Found");
    }
}

/***** GET & POST ROUTES  *****/

// Method POST Routes
if(isset($_POST['action']) && $_POST['action']=="login_user"){
    $response = [];
    $post_data = $_POST;

    //Expecting Fields For User Registeration
    $fields = ['user_email', 'user_password'];

    //Validation of POST Fields Of User (not empty and also remove extra space)
    $validation = field_validator($fields, $post_data);
    if($validation['error']){
        $response = $validation;
    }else{
        $data = $validation['data'];
        $func = new Operation();
        $result = $func->select_with_multiple_condition(array('*'),'user',"user_email = '".$data['user_email']."'",'AND',"user_password = '".$data['user_password']."'");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $userId=$row["user_id"];
            }
            session_regenerate_id();
            $_SESSION['user_id']=$userId;
            session_write_close();
            $response['error'] = False;
            $response['user_id'] = $userId;
        }else{
            $response['error'] = True;
            $response['notify'] = "Invalid Credentials, try again with correct one.";
        }
    }
    json_response($response);
}

// Add User POST Action
if(isset($_POST['action']) && $_POST['action']=="add_user"){
    $response = [];
    $post_data = $_POST;

    //Expecting Fields For User Registeration
    $fields = ['user_fname', 'user_lname', 'user_email', 'user_password', 'user_gender', 'user_status'];
    //Validation of POST Fields Of User (not empty and also remove extra space)
    $validation = field_validator($fields, $post_data);
    if($validation['error']){
        json_response($validation);
    }else{
        $res = check_email_registered($post_data['user_email']);
        if($res){
            $response['error'] = False;
            $response['user_email'] = strval($post_data['user_email'])." Already Registered.";
        }else{
            $func = new Operation();
            $user_data = $validation['data'];
            $userFname = $user_data['user_fname'];
            $userLname = $user_data['user_lname'];
            $userEmail = $user_data['user_email'];
            $userPassword = $user_data['user_password'];
            $userStatus = $user_data['user_status'];
            $userGender = $user_data['user_gender'];
            $result = $func->insert('user',
                array('user_fname','user_lname','user_email','user_password','user_status','user_gender'),
                array("'$userFname'", "'$userLname'","'$userEmail'","'$userPassword'","'$userStatus'","'$userGender'")
            );
            if($result === TRUE){
                $response['error'] = False;
                $response['notify'] = strval($userEmail)." User Added Succesfull.";
            }else{
                $response['error'] = True;
                $response['notify'] = strval($userEmail)." Error While Adding.";
            }
        }
    }
    json_response($response);
}

// Delete User POST Action
if(isset($_POST['action']) && $_POST['action']=="delete_user"){
    $response = [];
    $post_data = $_POST;

    //Expecting Fields For User Delete
    $fields = ['user_id'];
    $validation = field_validator($fields, $post_data);
    if($validation['error']){
        json_response($validation);
    }else{
        $func = new Operation();
        $result = $func->delete('user', "user_id = '".$validation['data']['user_id']."'");
        if($result){
            $response['error'] = False;
            $response['notify'] = "User Deleted Sucessfully";
        }else{
            $response['error'] = True;
            $response['notify'] = "Error record not found";
        }
        json_response($response);
    }
}

// Method GET Routes
if(isset($_GET['action']) && $_GET['action'] != ""){
    switch ($_GET['action']) {
        case "all_user":
            list_users();
            break;
        case "logout":
            session_destroy();
            header("location: index.php");
            break;
        case "edit_user":
            break;
        case "edit_task":
            break;
        case "edit_":
            break;
        case "edit_user":
            break;
        default:

    }
}

