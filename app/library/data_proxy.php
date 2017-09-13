<?php 

class Proxy {

		/**
		 * Proxy Context
		 *
		 * @var array
		 */
		protected static function getContext($method = 'GET', $timeout, $data = NULL) {
			$html_array = array(
	        'method'  => $method,
					'header'  => 'Authorization: Basic ' . base64_encode(Config::get('proxy.username').':'.Config::get('proxy.password'))."\r\n",
					'timeout' => $timeout,
	        'ignore_errors' => true
				);
			if($method == 'POST'){
				$html_array['header'] = "Content-type: application/x-www-form-urlencoded\r\n".$html_array['header'];
			}
			
			if(!is_null($data) && (count($data) !== 0 )){
				$html_array['content'] = http_build_query($data);
			}

			return stream_context_create(array(
				'http' => $html_array
			));
		}

		protected static function processResponse($toJSON, $response, $header){
			if(!strpos($header[0], '200') && $response == ''){
				return $header[0];
			}
			if(!$response) { return false; }
			if($toJSON){
				$temp = json_decode($response);
				if(!is_null($temp) && $temp != ''){
					return json_decode($response);
				}
			}
			return $response;
		}

		public static function get($route, $data = null, $toJSON = true, $timeout = 30) {
			if(!is_null($data) && (count($data) !== 0 )){
				$tempRoute = parse_url($route);				
				if(isset($tempRoute['query'])){
					parse_str($tempRoute['query'], $output);				
					$data = array_merge($data, $output);
				}
				$route = $tempRoute['path'].'?'.http_build_query($data);
			}
			
			$response = file_get_contents(Config::get('proxy.location').$route, false, Proxy::getContext('GET', $timeout));
			return Proxy::processResponse($toJSON, $response, $http_response_header);
		}

		public static function post($route, $data, $toJSON = true, $timeout = 30) {
			$response = file_get_contents(Config::get('proxy.location').$route, false, Proxy::getContext('POST', $timeout, $data));
			return Proxy::processResponse($toJSON, $response, $http_response_header);
		}
}