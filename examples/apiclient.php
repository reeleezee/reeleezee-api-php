<?php
/*
Helper classes for the Client API

Licensed under MIT license
(c) 2017 Reeleezee BV
*/

    // WARNING: This function does not create truly unique uuid's
    //          Only used for demo purposes
    function uuid_v4() 
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    class JSONObject 
    {
        public function __construct($json = FALSE) 
        {
            if ($json) $this->set(json_decode($json, TRUE));
        }

        public function set($data) 
        {
            foreach ($data as $key => $value) 
            {
                if (is_array($value)) {
                    $sub = new JSONObject;
                    $sub->set($value);
                    $value = $sub;
                }
                $this->{$key} = $value;
            }
        }
    }

    class ApiClient
    {
        private $headers;
        private $uri;
        private $username;
        private $password;

        function __construct($uri, $headers, $username, $password) 
        {
            $this->uri = $uri;
            $this->headers = $headers;
            $this->username = $username;
            $this->password = $password;
        }

        public function GET($route)
        {
            return $this->do_request($route, 'GET');
        }

        public function PUT($route, $data)
        {
            return $this->do_request($route, 'PUT', $data);
        }        

        private function do_request($route, $method, $data = null)
        {
            $ch = curl_init($this->uri . $route);

            curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);

            if ($method == 'POST') 
            {
                curl_setopt($ch, CURLOPT_POST ,TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
            }
            else if ($method == 'PUT') 
            {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            else if ($method == 'DELETE') 
            {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }	

            curl_setopt($ch, CURLOPT_HTTPHEADER , $this->headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,TRUE);
            curl_setopt($ch, CURLOPT_HEADER ,FALSE);

            $content = curl_exec($ch);
            $result = curl_getinfo($ch);
            curl_close($ch);
            
            return new Response($result, $content); 
        }
    }

    class Response
    {
        private $result;
        public $content;
        public $json;
        public $http_code;
        public $content_type;
        public $isJSON;
        public $next_link;

        function __construct($result, $content) 
        {
            $this->result = $result;
            $this->http_code = $this->result['http_code'];
            $this->content_type = $this->result['content_type'];
            if (strpos($this->content_type, 'application/json;') === 0)
            {
                $this->json = $content;
                $this->content = json_decode($content);
                // Paging support
                if (isset($this->content->{'@odata.nextLink'}))
                {
                    if (preg_match('/\/api\/[^\/]+(.*)/', $this->content->{'@odata.nextLink'}, $matches))
                    {
                        $this->next_link = $matches[1];
                    }
                }
                $this->isJSON = TRUE;
            }
            else
            {
                $this->content = $content;
                $this->isJSON = FALSE;
            }
        }        
    }
?>