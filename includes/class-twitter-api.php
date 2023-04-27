<?php
define('TWITTER_RETURN_URL', 'https://promptslab.co/wp-admin/admin.php?page=pl-admin');

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterAPI {
    // for v1.1
    private $twitter_oauth;
    // for v2
    private $client_id;
    private $client_secret;
    private $auth_code;
    private $access_token;

    public function __construct() {
        $this->client_id = pl_option(PL_OPTION_TWITTER_CLIENT_ID);
        $this->client_secret = pl_option(PL_OPTION_TWITTER_CLIENT_SECRET);
        $this->auth_code = pl_option(PL_OPTION_TWITTER_AUTH_CODE);

        if (time() + 3600 < get_option('pl_access_token_expire', 0)) {
            $this->access_token = get_option('pl_access_token', null);
        }
    }

    private function get_oauth() {
        if (!$this->twitter_oauth) {
            $this->twitter_oauth = new TwitterOAuth(
                pl_option(PL_OPTION_TWITTER_CONSUMER_KEY), 
                pl_option(PL_OPTION_TWITTER_CONSUMER_SECRET), 
                pl_option(PL_OPTION_TWITTER_ACCESS_TOKEN), 
                pl_option(PL_OPTION_TWITTER_ACCESS_TOKEN_SECRET),
            );
        }
        
        return $this->twitter_oauth;
    }

    public function get_access_token() {
        if ($this->access_token) {
          return $this->access_token;
        }

        $refresh_token = get_option('pl_refresh_token', null);
        if ($refresh_token) {
            $data = [
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ];
        } else {
            $data = [
                'code' => $this->auth_code,
                'grant_type' => 'authorization_code',
                'client_id' => $this->client_id,
                'redirect_uri' => TWITTER_RETURN_URL,
                'code_verifier' => 'challenge',
            ];
        }
        
        $result = $this->api_request('oauth2/token', 'POST', $data, basic_auth: true);
        error_log(var_export($result, true));
        if (isset($result['error'])) {
            $this->access_token = null;
            update_option('pl_access_token', null);
            update_option('pl_refresh_token', null);
            update_option('pl_access_token_expire', 0 );
        } else {
            $this->access_token = $result['access_token'];
            update_option('pl_access_token', $result['access_token']);
            update_option('pl_refresh_token', $result['refresh_token']);
            update_option('pl_access_token_expire', time() + $result['expires_in'] );
        }
        return $this->access_token;
    }

    function auth_url() {
        if (!$this->client_id) {
            return null;
        }
        return $this->build_auth_url(
            $this->client_id,
            TWITTER_RETURN_URL,
            [
                'tweet.read',
                'tweet.write',
                'offline.access',
                'users.read',
            ],
        );
    }

    function build_auth_url($client_id, $redirect_uri, $scopes, $state = 'state', $code_challenge = 'challenge', $code_challenge_method = 'plain') {
        $base_url = 'https://twitter.com/i/oauth2/authorize';
        $query_params = [
          'response_type' => 'code',
          'client_id' => $client_id,
          'redirect_uri' => $redirect_uri,
          'scope' => implode(' ', $scopes),
        ];
      
        if ($state !== null) {
          $query_params['state'] = $state;
        }
      
        if ($code_challenge !== null) {
          $query_params['code_challenge'] = $code_challenge;
          $query_params['code_challenge_method'] = $code_challenge_method;
        }
      
        // Use PHP_QUERY_RFC3986 to encode white space to %20 instead +
        $query_string = http_build_query($query_params, encoding_type: PHP_QUERY_RFC3986);
        return $base_url . '?' . $query_string;
    }    

    private function api_request($endpoint, $method = 'GET', $data = null, $rawBody = null, $headers = null, $basic_auth = false) {
        $url = 'https://api.twitter.com/2/' . $endpoint;
    
        // Initialize cURL
        $curl = curl_init();
    
        // Set the request URL
        curl_setopt($curl, CURLOPT_URL, $url);
    
        // Set the HTTP method
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    
        // Set the request headers
        $requiredHeaders = array(
            "Content-Type: application/json"
        );

        if ($basic_auth) {
            curl_setopt($curl, CURLOPT_USERPWD, "{$this->client_id}:{$this->client_secret}");
        } else {
            $requiredHeaders[] = "Authorization: Bearer {$this->get_access_token()}";
        }

        $allHeaders = $headers == null ? $requiredHeaders : array_merge($requiredHeaders, $headers);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $allHeaders);
    
        // Set the request data
        if ($method == 'POST' || $method == 'PUT') {
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
            if ($rawBody) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $rawBody);
            }
        }
    
        // Return the response as a string instead of outputting it directly
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
        // Execute the request and retrieve the response
        $response = curl_exec($curl);
    
        // Check if the request was successful
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new Exception($error);
        }
    
        // Get the response status code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        // Close the cURL handle
        curl_close($curl);
    
        if ($statusCode >= 200 && $statusCode < 300) {
            // Request was successful
            return json_decode($response, true);
            // Do something with $responseData
        } else {
            // Request failed
            return json_decode($response, true);
            // Handle the error
        }
    }

    public function post_tweet_with_images($text, $image_paths) {
        $media_ids = array();
        foreach ($image_paths as $path) {
            $media = $this->get_oauth()->upload('media/upload', ['media' => $path]);
            $media_ids[] = $media->media_id_string;
        }

        $data = [
            'text' => $text,
            'media' => [
                'media_ids' => $media_ids,
            ]
        ];

        $result = $this->api_request('tweets', 'POST', $data);
        if (isset($result['data']['id'])) {
            return $result['data']['id'];
        }
        error_log(var_export($result, true));
    }

    public function delete_tweet($tweet_id) {
        $result = $this->api_request('tweets/' . $tweet_id, 'DELETE');
        return $result;
    }
}

