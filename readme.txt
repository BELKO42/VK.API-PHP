# VK.API-PHP
PHP CLASS для работы с VK.API

```
	{APP_ID} - Ид приложения
	{SECRET_KEY} - Секретный ключ приложения
	{LOCALE} - Язык вывода(ru или en, если пустой автоматически ru)
	{REDIRECT_URI} - Урл редиректа
	{CODE} - $_GET['code'] после {REDIRECT_URI}
	{ACCESS_TOKEN} - Секретный токен
	{UID} - User_ID VK
	{TYPE_OUT} - в какой формате делать вывод(при вводе функции json перекодирует ответ VK в многомерный массив, иначе оставляет ответ сервера VK неизменным)
	{DOCUMENTATION_VK} - Документция для работы с VK.API - https://vk.com/dev/methods
	
	{METHOD} - Метод(users, photos, wall и.тд) - писать их нужно как указано в {DOCUMENTATION_VK} (регистр чувствителен)
	{NAME_METHOD} - ПодМетод(get, isAppUser, search и.тд) - писать их нужно как указано в {DOCUMENTATION_VK} (регистр чувствителен)
	{ARRAY_METHOD} - Массив данных для отправки вида: array('name'=>'value','name1'=>'value1') : [name] - данная которую требует VK, [value] - значение данной которую требует VK 
```
```
<?
	$vk['app_id'] = '{APP_ID}';
	$vk['api_secret'] = '{SECRET_KEY}';
	$vk['locale'] = '{LOCALE}';
	$vk['redirect_uri'] = '{REDIRECT_URI}';
?>
```
##### INCLUDE FILE CLASS
```
	include_once('inc.class.VKAPI.php');
```
##### Объявление Класса
```
	$VK = new VK($vk['app_id'],$vk['api_secret'],$vk['locale']);
```
##### Получение ссылки для авторизации
```
	echo $VK->getAuth($vk['redirect_uri']);
```
Перейти по ссылке, авторизироваться, и ВК отправит на {REDIRECT_URI}.
Там получить {ACCESS_TOKEN}.
##### Получение {ACCESS_TOKEN}
```
	$token = $VK->getAccessToken($_GET['code'],$vk['redirect_uri'])->json();
```
##### Занесение {ACCESS_TOKEN} и {UID} в сессию
```
	$VK->setAccessToken($token['access_token']);
	$VK->setUid($token['user_id']);
```
##### Вытащить {ACCESS_TOKEN} и {UID} с сессии
```
	$VK->token();//access_token
	$VK->uid();//uid
```
После всех этих манипуляций, можно использовать полностью, все функции.
##### Как работает основной вызов функции
```
$var = $VK->{METHOD}('{NAME_METHOD}',{ARRAY_METHOD})->{TYPE_OUT}();
```
###### Пример вызова users.get с выводом всех данных
```
$users_get = $VK->users('get', array('user_ids'=>$VK->uid(),'fields'=>'all','access_token'=>$VK->token()))->json();
//Если имя данной = 'fields' и значение ее 'all' то выводится вся информация(иначе что нужно вывести, через запятую подробности в {DOCUMENTATION_VK})
```
