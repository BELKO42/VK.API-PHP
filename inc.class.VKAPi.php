<?
	session_start();
	error_reporting('E_ALL');
	ini_set('display_errors','ON');
	//include_once('db.php');/////////////////////////////////////DATE BASE////////
	//include_once('cms-belko.php');//////////////////////////////SETTINGS BELKO///
	//include_once('inc/function.inc.php');///////////////////////FUNCTION/////////
	//include_once('inc/class.inc.php');//////////////////////////CLASS////////////
	//define('ROOT', SETTINGS::root());///////////////////////////ROOT/////////////
	//define('ROOT1', SETTINGS::root1());/////////////////////////ROOT1////////////
	//include_once('inc/settings.inc.php');///////////////////////SETTINGS/////////
	
	class VK{
		private $__result;
		private $__method;
		private $__name_method;
		private $app_id;//////////ИД приложения
		private $api_secret;//////Секретный код приложения
		private $access_token;////Секретный ТОКЕН
		private $uid;/////////////Юзер ид
		private $locale;//////////Язык отправки на сервер ВК
		const AUTHORIZE_URL = 'https://oauth.vk.com/authorize';//Урл авторизации
		const ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';//урл токена
		const URL = 'https://api.vk.com/method/'/*users.get?user_ids=113309621*/;//урл методов
		const ALL_FIELDS = 'id,first_name,last_name,nickname,sex,bdate,city,country,home_town,photo_50,photo_100,photo_200,photo_200_orig,photo_400_orig,photo_max,photo_max_orig,online,site,education,universities,schools,status,last_seen,connections,screen_name,maiden_name';//все поля для вывода
		/*Конструктор*/
		public function __construct($app_id,$api_secret,$locale){
			$this->app_id = $app_id;
			$this->api_secret = $api_secret;
			$this->scope = 'friends,photos,audio,video,docs,notes,status,groups,email,notifications,stats,ads,offline';
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
				}else /*if($key=='q')*/{
					$res.= $key.'='.urlencode($value).'&';
				}/*else{
					$res.= $key.'='.$value.'&';
				}*/
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
	
		
		/**/
		public function json(){
			return (array)json_decode($this->__result);
		}
		
		public function __toString(){
			return $this->__result;
		}
		/**/
	}

$vk['app_id'] = '5168448';
$vk['api_secret'] = 'SsKrU2R2WZiLzMg2MMemkD6K5';
$vk['locale'] = 'ru';
$vk['redirect_uri'] = 'http://www.fleamarket.moscow/vkapi.php';
	$VK = new VK($vk['app_id'],$vk['api_secret'],$vk['locale']);
		/*echo '<pre>';
		print_r($VK->users('get',array('user_ids'=>'239638833','fields'=>'all')));
		echo $VK->users('get',array('user_ids'=>'239638833','fields'=>'all'));
		echo '</pre>';
		exit;*/
		if($_GET['code']!=''){
			$token = $VK->getAccessToken($_GET['code'],$vk['redirect_uri'])->json();
			//$token = $VK->getAccessToken($_GET['code'],$vk['redirect_uri'])->json();
			print_r($token);
			//echo '///'.$token['access_token'].'///<br>';
			$VK->setAccessToken($token['access_token']);
			$VK->setUid($token['user_id']);
		}else{
			echo $VK->getAuth($vk['redirect_uri']).'<br>';
			//echo '+++'.$VK->token().'+++<br>';
			
			echo '###<pre>';
			//print_r($VK->users('get', array('user_ids'=>$VK->uid(),'fields'=>'all','access_token'=>$VK->token()))->json());
			//print_r($VK->users('isAppUser', array('user_id'=>'124757551','access_token'=>$VK->token()))->json());
			//print_r($VK->users('search', array('q'=>'Юрий Мошкин','count'=>'5','fields'=>'sex','access_token'=>$VK->token()))->json());
			print_r($VK->users('getSubscriptions', array('user_id'=>$VK->uid(), 'offset'=>'0', 'extended'=>'1', 'count'=>'200', 'fields'=>'1'))->json());
			print_r($VK->users('getFollowers', array('user_id'=>$VK->uid(), 'offset'=>'0', 'extended'=>'1', 'count'=>'200', 'fields'=>'1'))->json());
			echo '</pre>###<br>';
		}
		//$VK = new VK($vk['app_id'],$vk['api_secret'],$vk['api_secret'],$vk['locale']);
		//print_r($VK->users('get',query(array('user_ids'=>'113309621','fields'=>'sex,photo_50'))));
?>
