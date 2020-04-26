<?php

// 
// Datafordeler Staging example
//

// Functions --

function CallAPI( $config, $method, $url, $data = false, $username = false, $password = false ) {

	// Set up headers --

	$headers = array(
		'Content-Type: application/json'
	);


	if( $config['token']) {
		array_push( $headers, 'Authorization: Bearer '.$config['token'] );
	}

	// Create session --

	$curl = curl_init();
	
    // Configure session --

	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
	// curl_setopt($curl, CURLOPT_PROXY, "127.0.0.1:8888");
	switch ($method)
	{
		case "LOGIN":
		curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);  
		case "POST":
		curl_setopt($curl, CURLOPT_POST, 1);
		if ($data) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		break;
		case "PATCH":
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
		if ($data) {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		break;
		default:
		if ($data)
			$url = sprintf("%s?%s", $url, http_build_query($data));
		print"\nCalling URL: ";
		print_r($url);
		print"\n\n";
	}

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // Perform query --

	$result = curl_exec($curl);

	curl_close($curl);

	$messages = json_decode($result, true)['messages'];
	if($messages['code'] != 0) {
		print "There was an error:\n".$result."\n";
	}

	return $result;

}

// Main --

date_default_timezone_set('Europe/Copenhagen');

// Prepare configuration variables --

$config = array();

$config["user"] = "robot-ipsum";
$config["password"] = "...";
$config["host"] = "filemakerserver.cphbusiness.dk";
$config["database"] = "DatafordelerStaging";
$config["layout"] = "XErrorLog";
$config['token'] = '';

// Headers --

print <<< END
 ___ __  __   ___ ___ ___ _____ 
| __|  \/  | | _ \ __/ __|_   _|
| _|| |\/| | |   / _|\__ \ | |  
|_| |_|  |_| |_|_\___|___/ |_|  
END;

// Set configuration variables from command line --

print "\n\n";
print "System:\n\tVersion: v.1.0.0\n\n";

print "Time:\n\tStarted at: ".date('d-m-Y H:i:s')."\n\n";

print "Configuration:\n";
print "\tUser: ".$config["user"]."\n";
print "\tHost: ".$config["host"]."\n";
print "\tDatabase: ".$config["database"]."\n";
print "\tLayout: ".$config["layout"]."\n";

// Log in --

$json = CallAPI(
	$config,
	"LOGIN",
	"https://".$config['host']."/fmi/data/v1/databases/".$config['database']."/sessions",
	json_encode(
		array()
	),
	$config['user'],
	$config['password']
);

$config['token'] = json_decode( $json, true )['response']['token'];

// Querying for a record executing the script via GET

$query = json_encode(array("activitycode" => "s16makfoobar"));

$result = CallAPI(
	$config,
	"GET",
	"https://".$config['host']."/fmi/data/v1/databases/".$config['database']."/layouts/".$config['layout']."/records/1?script=ActivityDetails&script.param=".$query,
	array()
);

print "Result:\n\n".print_r($result, true)."\n";

print "Done.\n";

?>
