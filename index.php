<?php
	
$user = "Mike"; 
$pass = "Rangerman";  
$DevID_Check = '93d93b2907a59a503188be3753451bd7';

// Some data to send - base64
$myObj = new StdClass; 
$myObj->name = base64_encode("Mark Joyce");
$myObj->age = base64_encode(43);
$myObj->city = base64_encode("Beechboro");

////////////////////////////////
// Error Handler - Try Catch  ///////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////
set_error_handler('exceptions_error_handler'); // Dont want to give away whats going on.

function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}

////////////////////////////////////////////////////////////
//Function to check the current user                      ///////////////////////////////////////////////////////////
//Returns json, empty if not found and populated if in.   //
////////////////////////////////////////////////////////////
function checkUser($userDeviceID, $UID)
{
	$serverName = "(LocalDB)\MSSQLLocalDB"; 
	$connectionInfo = array( "Database"=>"G:\Rangers.mdf");  
	/* Connect using Windows Authentication. */  
	$conn = sqlsrv_connect( $serverName, $connectionInfo);  
	if( $conn === false )  
	{  
		 die("Unable to connect.</br>");  
		 //die( print_r( sqlsrv_errors(), true));  
	}  
	 
	// Query SQL Server for the login details mobile phone device id and their user name.	 
	$myQuery = "SELECT * FROM Rangers WHERE DeviceID = '".$userDeviceID."' AND FirstName = '".$UID."'" ;
	$tsql = $myQuery;
	$stmt = sqlsrv_query( $conn, $tsql);  
	if( $stmt === false )  
	{  
	 echo "Error in executing query.</br>";  
	 die( print_r( sqlsrv_errors(), true));  
	}   
	
	$res = [];
	$count = 1;
	while( $row = sqlsrv_fetch_array($stmt) ) 
	{
		$res['Data'.$count] = $row;	
		$count += 1;
	}
	sqlsrv_free_stmt($stmt);
	sqlsrv_close( $conn); 
	$Myjson = json_encode($res); 
	return $Myjson;

}

//////////////////////////
//   Get  DUMMY  DEBUG  ///////////////////////////////////////////////////////////////////////////////////////
//////////////////////////
function getData()
{
	$serverName = "(LocalDB)\MSSQLLocalDB"; 
	$connectionInfo = array( "Database"=>"G:\Rangers.mdf");  
	  
	/* Connect using Windows Authentication. */  
	$conn = sqlsrv_connect( $serverName, $connectionInfo);  
	if( $conn === false )  
	{  
		 echo "Unable to connect.</br>";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	  
	/* Query SQL Server for the login of the user accessing the  
	database. */  
	//sql = "SELECT CONVERT(varchar(32), SUSER_SNAME())";  
	//$tsql = "SELECT * from Rangers WHERE RangerID = 12345";
	$tsql = "SELECT * FROM Rangers";
	$stmt = sqlsrv_query( $conn, $tsql);  
	if( $stmt === false )  
	{  
		 echo "Error in executing query.</br>";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	  
	/* Retrieve and display the results of the query. */  
	//$row = sqlsrv_fetch_array($stmt);  

	$res = [];
	$count = 1;
	while( $row = sqlsrv_fetch_array($stmt) ) {
	// you need SQLSRV_FETCH_NUMERIC for your result, but i prefere to use SQLSRV_FETCH_ASSOC
		$res['Data'.$count] = $row;	
		$count += 1;
	}

	sqlsrv_free_stmt($stmt);
	sqlsrv_close( $conn); 
	$Myjson = json_encode($res);
	//echo $Myjson."</br>";
	return $Myjson;
	 
}
/////////////////////////////////////////////////
//   Retrieve Ranger Jobs from the Database.   /////////////////////////////////////////////////////////
/////////////////////////////////////////////////
function getJobs()
{
	$serverName = "(LocalDB)\MSSQLLocalDB"; 
	$connectionInfo = array( "Database"=>"G:\Rangers.mdf");  
	  
	/* Connect using Windows Authentication. */  
	$conn = sqlsrv_connect( $serverName, $connectionInfo);  
	if( $conn === false )  
	{  
		 echo "Unable to connect.</br>";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	  
	/* Query SQL Server for the login of the user accessing the  
	database. */  
	//sql = "SELECT CONVERT(varchar(32), SUSER_SNAME())";  
	//$tsql = "SELECT * from Rangers WHERE RangerID = 12345";
	$tsql = "SELECT JobType FROM Job";
	$stmt = sqlsrv_query( $conn, $tsql);  
	if( $stmt === false )  
	{  
		 echo "Error in executing query.</br>";  
		 die( print_r( sqlsrv_errors(), true));  
	}  
	  
	/* Retrieve and display the results of the query. */  
	//$row = sqlsrv_fetch_array($stmt);  

	$res = [];
	$count = 1;
	while( $row = sqlsrv_fetch_array($stmt) ) {
	// you need SQLSRV_FETCH_NUMERIC for your result, but i prefere to use SQLSRV_FETCH_ASSOC
		$res['Data'.$count] = $row;	
		$count += 1;
	}

	sqlsrv_free_stmt($stmt);
	sqlsrv_close( $conn); 
	$Myjson = json_encode($res);
	//echo $Myjson."</br>";
	return $Myjson;
}

////////////////////////////////////////
//      Main Code Begin               ///////////////////////////////////////////////////////////
////////////////////////////////////////

try
{  // Get Post Data From Mobile Phone. 
	$Name = $_POST["userID"]; 
	$Pass = $_POST["password"]; 
	$DevID = $_POST["devID"];
	$myObj->devID = $DevID;
}
catch(ErrorException $e)
{
	die ("Try catch error");
}

if($DevID != $DevID_Check) //change this this refer to the database
{
	$myObj->name = base64_encode("ERROR");
	$myObj->age = base64_encode("ERROR");
	$myObj->city = base64_encode("ERROR");
	$myObj->devID = ("ERROR");
	
	$myJSON = json_encode($myObj); //Data to send
	echo $myJSON;
	die ("ID ERROR");

}

if ($Name == "" or $Pass == "") 
{
	die("Error");
}

else
{
	//$Data = getData();
	$Data = checkUser($DevID_Check, base64_decode($Name));
	//echo $Data;
	$json_array  = json_decode($Data, true);
    $elementCount  = count($json_array);
	
	if ($elementCount == 0)
	{
		$myObj->name = base64_encode("ERROR");
		$myObj->age = base64_encode("ERROR");
		$myObj->city = base64_encode("ERROR");
		$myObj->devID = ("ERROR");
		
		$myJSON = json_encode($myObj); //Data to send
		echo $myJSON;
		die ("ID ERROR");
	}
	else
	{
		$Data = getJobs();
		echo $Data;
	}
	
	
	/*
	for ($x = 0; $x < 10; $x++)
	{
		$Name = base64_decode($Name);
	}
	
	if ((base64_decode($Name) == $user) and (base64_decode($Pass) == $pass))
	{
		//echo "I know you come on in!";
		$myJSON = json_encode($myObj); //Data to send
		echo $myJSON;
	}
	else
	{
		die("Go away, you are not allowed in!");
	}
	*/
}

// Main Code End. 

?>
