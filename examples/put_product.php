<?php
/*
Create / Update a product

Licensed under MIT license
(c) 2017 Reeleezee BV
*/
include 'apiclient.php';
include 'settings.php';

function put_product() 
{
	global $uri, $headers;
	global $username, $password;
	$route = '/Products/';
	
	try 
	{
		$guid = uuid_v4();	// Please read the warning about this guid

		$product = new stdClass();
        $product->Description = 'New product from API';
        $product->SearchName = 'New API product';
        $product->Comment = 'This product is created by the PHP API client with id: ' . $guid;
        $product->Price = 12.55;

		$client = new ApiClient($uri, $headers, $username, $password);
		$response = $client->PUT($route . $guid, json_encode($product));
		if ($response->http_code == 200 && $response->isJSON) 
		{
			print (json_encode($response->content, JSON_PRETTY_PRINT));
			$product = new JSONObject($response->json);
				printf("%-38s %-40s %-20s %.2f" . PHP_EOL, 
					$product->id, substr($product->Description, 0, 40), substr($product->SearchName, 0, 20), $product->Price);				
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

put_product();
?>
