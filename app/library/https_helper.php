<?php 

class https_helper {

		Protected $path;
		Protected $username;
		Protected $password;

		public function https_helper ($config) {
			// dd($config);
			$this->path = $config->target;
			$this->username = $config->username;
			$this->password = Crypt::decrypt($config->password);
		}

		/**
		 * Context
		 *
		 * @var array
		 */
		protected function getContext($method = 'GET', $timeout, $data = NULL) {
			$html_array = array(
	        'method'  => $method,
					'header'  => 'Authorization: Basic ' . base64_encode($this->username.':'.$this->password)."\r\n",
					'timeout' => $timeout,
	        'ignore_errors' => true
				);
			if($method != 'GET'){
				$html_array['header'] = "Content-type: application/x-www-form-urlencoded\r\n".$html_array['header'];
			}
			
			if(!is_null($data) && (count($data) !== 0 )){
				$html_array['content'] = http_build_query($data);
			}

			return stream_context_create(array(
				'http' => $html_array
			));
		}

		protected function processResponse($toJSON, $response, $header){
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

		public function get($route, $data = null, $toJSON = true, $timeout = 30) {
			if(!is_null($data) && (count($data) !== 0 )){
				$tempRoute = parse_url($route);				
				if(isset($tempRoute['query'])){
					parse_str($tempRoute['query'], $output);				
					$data = array_merge($data, $output);
				}
				$route = $tempRoute['path'].'?'.http_build_query($data);

			}

			$response = file_get_contents($this->path.$route, false, $this->getContext('GET', $timeout));
			return $this->processResponse($toJSON, $response, $http_response_header);
		}

		public function post($method, $route, $data, $toJSON = true, $timeout = 30) {
			$response = file_get_contents($this->path.$route, false, $this->getContext($method, $timeout, $data));
			return $this->processResponse($toJSON, $response, $http_response_header);
		}

}