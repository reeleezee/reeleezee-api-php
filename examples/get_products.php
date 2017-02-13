<?php
/*
Get a list of products

Licensed under MIT license
(c) 2017 Reeleezee BV
*/
include 'apiclient.php';
include 'settings.php';

function get_products() 
{
	global $uri, $headers;
	global $username, $password;
	$route = '/Products';
	
	try 
	{
		$client = new ApiClient($uri, $headers, $username, $password);

		do
		{
			$response = $client->GET($route);
			if ($response->http_code == 200 && $response->isJSON) 
			{
				$products = new JSONObject($response->json);
				foreach ($products->value as $product)
				{
					printf("%-38s %-40s %-20s %.2f" . PHP_EOL, 
						$product->id, substr($product->Description, 0, 40), substr($product->SearchName, 0, 20), $product->Price);				
				}
				if ($response->next_link != null)
				{
					$route = $response->next_link;
				}
			}
			else
			{
				print $response->http_code;
			}
		} while ($response->http_code == 200 and $response->next_link != null);
		
	} catch (Exception $e) 
	{
		print $e->getMessage() . PHP_EOL;
	}
}

get_products();
?>
