<?
	session_start();//////////////////////START_SESSION
	error_reporting('E_ALL');/////////////Вывод ошибок
	ini_set('display_errors','ON');//\\\\\Вывод ошибок
	
	class VK{
		private $__result;////////\\\\\\\\\\\\\\|
		private $__method;////////-НЕТРОГАТЬ!!!|
		private $__name_method;/////////////////|
		private $app_id;//////////ИД приложения
		private $api_secret;//////Секретный код приложения
		private $access_token;////Секретный ТОКЕН
		private $uid;/////////////Юзер ид
		private $locale;//////////Язык отправки на сервер ВК
		const AUTHORIZE_URL = 'https://oauth.vk.com/authorize';//Урл авторизации
		const ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';//урл токена
		const URL = 'https://api.vk.com/method/';//урл методов
		const ALL_FIELDS = 'id,first_name,last_name,nickname,domain,sex,bdate,city,country,home_town,photo_50,photo_100,photo_200,photo_200_orig,photo_400_orig,photo_max,photo_max_orig,online,site,education,universities,schools,status,last_seen,connections,screen_name,maiden_name';//все поля для вывода
		/*Конструктор*/
		public function __construct($app_id,$api_secret,$locale){
			$this->app_id = $app_id;
			$this->api_secret = $api_secret;
			$this->scope = 'friends,photos,audio,video,docs,notes,pages,status,wall,groups,email,notifications,stats,ads,market,offline';//messages,
			if($locale=='ru'){
				$opts = array(
					'http'=>array(
						'header'=>"Accept-language: ru\r\n"
					)
				);
				$local = stream_context_create($opts);
				$this->locale = $local;
			}else
			if($locale=='en'){
				$opts = array(
					'http'=>array(
						'header'=>"Accept-language: en\r\n"
					)
				);
				$local = stream_context_create($opts);
				$this->locale = $local;
			}else
			if($locale==''){
				$opts = array(
					'http'=>array(
						'header'=>"Accept-language: ru\r\n"
					)
				);
				$local = stream_context_create($opts);
				$this->locale = $local;
			}
		}
		/*\Конструктор*/
		
		function setAccessToken($access_token){
			$this->access_token = $access_token;
			$_SESSION['access_token'] = $access_token;
		}
		
		function setUid($uid){
			$this->uid = $uid;
			$_SESSION['uid'] = $uid;
		}

		
		function getAuth($redirect){
			$url = 'http://oauth.vk.com/authorize';
			$params = array(
				'client_id'     => $this->app_id,
				'redirect_uri'  => $redirect,
				'response_type' => 'code',
				'scope' => $this->scope
			);
			return $url . '?' . urldecode(http_build_query($params));
		}
		
		function getAccessToken($code,$redirect){
			$this->__result = null;
			$params = array(
				'client_id' => $this->app_id,
				'client_secret' => $this->api_secret,
				'code' => $code,
				'redirect_uri' => $redirect
			);
			$token = file_get_contents(self::ACCESS_TOKEN_URL.'?'.urldecode(http_build_query($params)),false,$this->locale);
			$this->__result = $token;
			return $this;
		}
		
		function query($query){
			ksort($query);
			foreach($query as $key => $value){
				if($key=='fields' && $value=='all'){
					$res.= $key.'='.self::ALL_FIELDS.'&';
				}else{
					$res.= $key.'='.urlencode($value).'&';
				}
			}
			$res = substr($res,0,-1);
			$href = self::URL.$this->__method.'.'.$this->__name_method.'?'.$res;
			return $href;
		}
		
		public function users($method, $query){
			$this->__result = null;
			$this->__method = 'users';
			$this->__name_method = $method;
			
			$array_name_method = array('get','search','isAppUser','getSubscriptions','getFollowers');
			
			if(in_array($this->__name_method,$array_name_method)){
				$query = $this->query($query);
				$get = file_get_contents($query,false,$this->locale);
				$this->__result = $get;
				return $this;
			}else{echo 'ERROR';}
		}
		
		public function photos($method, $query){
			$this->__result = null;
			$this->__method = 'photos';
			$this->__name_method = $method;
			
			$array_name_method = array('get','getUploadServer','save');
			
			if(in_array($this->__name_method,$array_name_method)){
				$query = $this->query($query);
				$get = file_get_contents($query,false,$this->locale);
				$this->__result = $get;
				return $this;
			}else{echo 'ERROR';}
		}
		
		function token(){
			if($this->access_token!=''){
				return $this->access_token;
			}else{
				return $_SESSION['access_token'];
			}
		}
		
		function uid(){
			if($this->uid!=''){
				return $this->uid;
			}else{
				return $_SESSION['uid'];
			}
		}
		
		function upload_server($file_array,$upload_url){
			$this->__result = null;
			for($i=1;$i<count($file_array)+1;$i++){
				$data['file'.$i] = '@'.$file_array[$i-1];
			}
			$ch = curl_init($upload_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$response = curl_exec($ch);
			$this->__result = $response;
			curl_close($ch);
			return $this;
		}

	
		public function json(){
			return (array)json_decode($this->__result);
		}
		
		public function __toString(){
			return $this->__result;
		}
	}
?>
