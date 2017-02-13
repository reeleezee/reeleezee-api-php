<?php
/*
Get User information

Licensed under MIT license
(c) 2017 Reeleezee BV
*/
include 'apiclient.php';
include 'settings.php';

function get_userinfo() 
{
	global $uri, $headers;
	global $username, $password;
	$route = '/UserInfo?$expand=*';
	
	try 
	{
		$client = new ApiClient($uri, $headers, $username, $password);
		$response = $client->GET($route);
		if ($response->http_code == 200 && $response->isJSON) 
		{
			$json = json_encode($response->content, JSON_PRETTY_PRINT);
			print($json);
		}
		else
		{
			print $response->content;
		}
	} catch (Exception $e) 
	{
		print $e->getMessage() . PHP_EOL;
	}
}

get_userinfo();
?>
