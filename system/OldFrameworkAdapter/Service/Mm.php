<?php

namespace CodeHuiter\OldFrameworkAdapter\Service;

/**
 * Mades model for CodeIgniter. 
 * v2.6.0.110
 * Last Mod: 2016-12-11
 * 
 */
class Mm {


	public $created_file_mode = 0777;
	
	/** $app_properties
	 * Свойства Приложения
	 * @var type 
	 */
	public $app_properties = array(
	);
	public $app_properties_to_data = array();
	
	public $compressor = array();
	public $models = array();
	
	/** $domain_type
	 * Тип текущего кастомного домена
	 * @var type 
	 */
	public $domain_type = '';
	public $domain_types = array( 
	);
	
	/** $app_languages
	 * Поддерживаемые языки
	 * @var type 
	 */
	public $app_languages = array('russian','english','testlanguage'); // EXAMPLE
	public $current_language = '';
	/** $storage_directories
	 * Места хранения файлов
	 * @var type 
	 */
	public $storage_directories = array(
	);
	
	/** $url_aliases
	 * Псевдонимы Url используемые в createUrl
	 * @var type 
	 */
	public $url_aliases = array(
	);
	
	/** $license
	 * Строка лицензии, задаётся в конфиге
	 * @var type 
	 */
	public $license = 'unlicensed';
	
	public $sqlDebug = null;
	public $sqlDebugPrint = null;
	public $sqlDebugTime = null;
	
	public function __construct() {
		parent::__construct();
		// Установка стандартных значений модуля
		$this->db_now = " CONVERT_TZ(NOW(), ".$this->db_current_timezome.", ".$this->db_store_timezone.") ";
		date_default_timezone_set('UTC');
		
		// Загрузка Свойств Приложения
		require(APPPATH . 'config/mm_config.php');
		if (isset($mm)){
			if(isset($mm['app_properties'])){
				$this->app_properties = $mm['app_properties'];
				$this->sqlDebug = $this->app_properties['sql_debug'];
				$this->sqlDebugPrint = $this->app_properties['sql_debug_print'];
				$this->sqlDebugTime = $this->app_properties['sql_debug_time'];
			}
			if(isset($mm['app_properties_to_data'])){
				$this->app_properties_to_data = $mm['app_properties_to_data'];
			}
			if(isset($mm['license'])){
				$this->license = $mm['license'];
			}
			if(isset($mm['domain_types'])){
				$this->domain_types = $mm['domain_types'];
			}
			if(isset($mm['app_languages'])){
				$this->app_languages = $mm['app_languages'];
			}
			if(isset($mm['storage_directories'])){
				$this->storage_directories = $mm['storage_directories'];
			}
			if(isset($mm['url_aliases'])){
				$this->url_aliases = $mm['url_aliases'];
			}
			if(isset($mm['compressor'])){
				$this->compressor = $mm['compressor'];
			}
			if(isset($mm['models'])){
				$this->models = $mm['models'];
			}
		}
		
		// Определение текущего кастомного домена.
		foreach($this->domain_types as $domain_type => $domainParams){
			if (strpos($_SERVER['HTTP_HOST'],$domainParams['domain_search']) !== false){
				$this->domain_type = $domain_type;
				// Обновление свойств для кастомного домена
				$this->setDefaultsForDomain();
				break;
			}
		}
		// Дефолтный язык
		$this->current_language = $this->g($this->app_languages[0]);
		
		// Подмена {#locale} во всех путях хранения для текущего домена
		foreach($this->storage_directories as $content => $store_info){
			if (isset($this->app_properties['dir_locale']) && $this->app_properties['dir_locale']){
				$this->storage_directories[$content]['store'] = str_replace('{#locale}', $this->app_properties['dir_locale'], $store_info['store']);
			}
			if($store_info['server_root'] === 'DOCUMENT_ROOT'){
				$this->storage_directories[$content]['server_root'] = $_SERVER['DOCUMENT_ROOT'];
			}
		}
		

		// Загрузка Базы Данных
		if ($this->domain_type && $this->domain_types[$this->domain_type]['database']){
			$this->setDb($this->domain_types[$this->domain_type]['database']);
		} else {
			$this->setDb();
		}
	}
	/** setDefaultsForDomain
	 * Обновляет Свойства Приложения от установленного домена
	 * @return boolean
	 */
	public function setDefaultsForDomain(){
		if (!$this->domain_type) return false;
		foreach($this->domain_types[$this->domain_type] as $key => $val){
			if (isset($this->app_properties[$key])){
				$this->app_properties[$key] = $val;
			}
		}
		return true;
	}
	/** setDefaultsLangsForDomain
	 * Обновляет Свойства Приложения от установленного языка (усли необходимо)
	 * @return boolean
	 */
	public function setDefaultsLangsForDomain(){
		
		return true;
	}
	/** getLanguage
	 * Возвращает Язык отображения сайта в зависимости
	 * <br/>1) Установленных куки (совпадающих с разрешёнными языками) 
	 * <br/>2) Настройки кастомного домена
	 * <br/>3) Первый Язык из разрешённых
	 * @param type COOKIE значение установленного языка
	 * @return type	Язык для использования в Приложении
	 */
	public function getLanguage(&$language){
		if (isset($language) && in_array($language,$this->app_languages)){
			$this->current_language = $language;
		} else {
			if ($this->domain_type){
				$domainLanguage = $this->domain_types[$this->domain_type]['language'];
				if($domainLanguage && in_array($domainLanguage, $this->app_languages)){
					$this->current_language = $domainLanguage;
					return $this->current_language;
				}
			}
			$this->current_language = $this->app_languages[0];
		}
		return $this->current_language;
	}
	/** firstData
	 * Инициализирует стандартные Данные от входных переменных
	 * @return boolean
	 */
	public function firstData(){
		if ($this->g($_GET['ref_id'])){
			setcookie('ref_id', $_GET['ref_id'], time()+3600*24*30, '/');
		}
		$ret = array();
		if($this->useAction() === false){
			$ret['error404'] = true;
		}
		if($this->g($_GET['body_ajax'])
				&& isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' 
		){
			$ret['body_ajax'] = $_GET['body_ajax'];
		} else {
			$ret['body_ajax'] = false;
		}
		if (isset($_SERVER['SUBDOMAIN_DIR'])){
			$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SUBDOMAIN_DIR']));
		}
		foreach($this->app_properties_to_data as $key){
			$ret[$key] = $this->app_properties[$key];
		}
		return $ret;
	}
	
	public function compressorInit(){
		if ($this->domain_type && isset($this->compressor[$this->domain_type])){
			$this->compressor = $this->compressor[$this->domain_type];
		}
				
		$serverdir = rtrim($_SERVER['DOCUMENT_ROOT'],'/');
		$file = $this->compressor['dir'] . '/' . $this->compressor['names'] . '_' . $this->compressor['version'];
		$exts = array('css','js');
		$this->compressor['result'] = array();
		foreach($exts as $ext){
			if (!file_exists($serverdir . $file . '.' . $ext) || $this->compressor['version'] == 'dev'){
				$fp = fopen($serverdir . $file . '.' . $ext, 'w');
				foreach($this->compressor[$ext] as $connected){
					$connectedExtArr = explode('.',$connected);
					if (end($connectedExtArr)==='php'){
						$connected_content = $this->load->view('../../public_html/'.$connected,array(),true);
					} else {
						$connected_content = file_get_contents($serverdir . $connected);
					}
					if (true) { // remove comments
						$connected_content = preg_replace('!/\*.*?\*/!s', '', $connected_content);
						$connected_content = preg_replace('/\n\s*\n/', "\n", $connected_content);
					}
					fwrite($fp, "/* $connected */ \n" . $connected_content ."\n");
				}
				fclose($fp);
			}
			$this->compressor['result'][$ext] = $file . '.' . $ext;
			if ($this->compressor['version'] == 'dev'){
				$this->compressor['result'][$ext] .= '?t='.time();
			}
		}
	}
	
	//*******************
	//***Модуль Ошибок***
	//*******************
	private $last_message = '';
	private function setErrorMessage($str) {
		$this->last_message = $str;
		return false;
	}
	/** getErrorMessage
	 * Выдаёт последнее установленное сообщение об ошибке
	 * @return type
	 */
	public function getErrorMessage() {
		return $this->last_message;
	}
	
	//***********************
	//*** Основные фунции ***
	//***********************
	/** g
	 * Возращает Значение $par или $def или ''
	 * @param type $par Элемент который может не существовать
	 * @param type $def Значение поумолчанию
	 * @return type Итоговое значения
	 */
	public function g(&$par, $def = ''){
		if(isset($par) && !is_null($par)){
			return $par;
		} else {
			return $def;
		}
	}
	
	/** debugParam
	 * Выводит переменную на экран
	 * 
	 * @param type $ex Переменная
	 * @param type $detail Вывести детальную информацию var_dump
	 * @param type $output Вернуть данные из функции вместо вывода на экран (не работает с var_dump)
	 * @return string
	 */
	public function debugParam($ex, $detail = false, $output = false) {
		$ret = '<pre style="word-wrap: break-word;">';
		if ($detail) {
			echo $ret; $ret = '';
			var_dump($ex);
		}
		$ret .= print_r($ex,true);
		$ret .= '</pre>';
		if (!$output) echo $ret;
		return $ret;
	}
	
	/** url
	 * Генерирует Url по псевдониму
	 * Должны быть заданы в url_aliases В mm_config
	 * 
	 * @param type $param1 $alias
	 * @param type $param2 Параметры
	 * ...
	 */
	public function url(){
		$args = func_get_args();
		if (!count($args)) return '[NO ARGS]';
		if(!(isset($this->url_aliases[$args[0]]) && $this->url_aliases[$args[0]])){
			return '[ALIAS NO EXIST]';
		}
		$ret = $this->url_aliases[$args[0]];
		for($i = 1; $i < count($args); $i++){
			$ret = preg_replace('/{#param}/', $args[$i], $ret, 1);
		}
		if($this->app_properties['urls_enable'] === TRUE){
			$this->load->library('site_urls');
			return $this->site_urls->getUrl($ret);
		} 
		return $ret;
	}
	
	public function callCallable($method, $opt){
		if (!is_callable($method)) return '[NOT CALLABLE FUNC]';
		return $method($opt);
	}
	
	
	protected $observer_events = array();
	public function observer_on($event,$func){
		if (!isset($this->observer_events[$event])){
			$this->observer_events[$event] = array();
		}
		$this->observer_events[$event][] = $func;
		return true;
	}
	public function observer_run($event,$params){
		if (!isset($this->observer_events[$event])) return false;
		foreach($this->observer_events[$event] as $key => $value){
			$this->callCallable($this->observer_events[$event][$key], $params);
		}
	}
	
	/** serverStore
	 * Возвращает Серверный путь до контента
	 * 
	 * @param type $content Тип контента
	 * @param type $path Остаточный путь до контента
	 * @param type $type Тип хранения контента
	 * @return string
	 */
	public function serverStore($content,$path){
		return $this->storage_directories[$content]['server_root'].$this->storage_directories[$content]['store'].$path;
	}
	/** store
	 * Возвращает HTTP путь до контента
	 * 
	 * @param type $content Тип контента
	 * @param type $path Остаточный путь до контента
	 * @param type $type Тип хранения контента
	 * @return string
	 */
	public function store($content, $path, $type = 0){
		if ($type == 0){
			return $this->storage_directories[$content]['site_url']
					.$this->storage_directories[$content]['store']
					.$path;
		}
		return 'unknown';
	}
	
	//***********************
	//*** Фунции БД *********
	//***********************
	private $dbl = null;
	public function setDb($dbName = ''){
		if ($dbName){
			$this->dbl = $this->load->database($dbName,true); 
		} else {
			$this->load->database(); 
			$this->dbl = $this->db;
		}
	}
	
	
	/** dbSelect
	 * Делает выборку из базы данных
	 * @param string $query_string Строка запроса
	 * @param boolean $debug Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return mixed Многомерный Массив результатов
	 */
	public function dbSelect($query_string, $fieldAsKey = false, $debug = false){
		//$this->dbl->query("RESET QUERY CACHE");
		//$this->dbl->query("FLUSH QUERY CACHE");
		$res = $this->dbl->query($query_string);
		$ret = array();
		if ($debug || $this->sqlDebug) { 
			$this->benchmark->mark('m_start');
		}
		foreach ($res->result_array() as $row) {
			$rower = array();
			if (get_magic_quotes_runtime()){
				foreach ($row as $key => $value) {
					$rower[$key] = stripcslashes($value);
				}
			} else {
				$rower = $row;
			}
			if ($fieldAsKey !== false) {
				$ret[$rower[$fieldAsKey]] = $rower;
			} else {
				$ret[] = $rower;
			}
			
		}
		if ($debug || $this->sqlDebug) { 
			$this->benchmark->mark('m_end');
			$elapsedDB = end($this->dbl->query_times);
			$elapsedFM = $this->benchmark->elapsed_time('m_start', 'm_end');
			if ($elapsedDB > $this->sqlDebugTime || $elapsedFM > $this->sqlDebugTime || $debug) {
				$message = "dbSelect:[format_time=".number_format($elapsedFM,6)."]\n:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}
		return $ret;
	}
	/** dbSelectOne
	 * Делает выборку одного элемента из Базы данных
	 * @param string $query_string Строка запроса (Желательно вконце LIMIT 0,1) само не доставляет
	 * @param boolean $debug Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return mixed Одномерный массив, строка результата
	 */
	public function dbSelectOne($query_string, $debug = false){
		$res = $this->dbl->query($query_string);
		if ($debug || $this->sqlDebug) { 
			$elapsedDB = end($this->dbl->query_times);
			if ($elapsedDB > $this->sqlDebugTime || $debug) {
				$message = "dbSelectOne:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}
		if ($res->num_rows() > 0) {
			$row = $res->row_array();
			if (get_magic_quotes_runtime()){
				foreach ($row as $key => $value) {
					$row[$key] = stripcslashes($row[$key]);
				}
			}
			return $row;
		} else { return false; }
	}
	/** dbSelectOneField
	 * Возвращает данные из поля одного результата
	 * @param string $query_string Строка запроса (Желательно вконце LIMIT 0,1) само не доставляет
	 * @param string $field Поле которое необходимо выбрать
	 * @param boolean $debug  Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return mixed
	 */
	public function dbSelectOneField($query_string, $field, $debug = false){
		$res = $this->dbl->query($query_string);
		if ($debug || $this->sqlDebug) { 
			$elapsedDB = end($this->dbl->query_times);
			if ($elapsedDB > $this->sqlDebugTime || $debug) {
				$message = "dbSelectOneField:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}
		if ($res->num_rows() > 0) {
			$row = $res->row_array();
			if (get_magic_quotes_runtime()){
				return stripcslashes($row[$field]);
			} else {
				return $row[$field];
			}
		} else { return false; }
	}
	/** dbSelectField
	 * Выбирает массив результатов одного поля
	 * @param type $query_string СТрока запроса
	 * @param type $field Поле которое необходимо выбирать
	 * @param type $debug  Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return mixed Одномерный массив результата одного поля
	 */
	public function dbSelectField($query_string, $field, $debug = false){
		$res = $this->dbl->query($query_string);
		$ret = array();
		foreach ($res->result_array() as $row) {
			if (get_magic_quotes_runtime()){
				$ret[] = stripcslashes($row[$field]);
			} else {
				$ret[] = $row[$field];
			}
		}
		if ($debug || $this->sqlDebug) { 
			$elapsedDB = end($this->dbl->query_times);
			if ($elapsedDB > $this->sqlDebugTime || $debug) {
				$message = "dbSelectField:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}	
		return $ret;
	}
	/** dbExecute
	 * Выполняет запрос в базу данных
	 * @param type $query_string Строка запроса
	 * @param type $debug  Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return int affected_rows
	 */
	public function dbExecute($query_string, $debug = false){
		$this->dbl->query($query_string);
		if ($debug || $this->sqlDebug) { 
			$elapsedDB = end($this->dbl->query_times);
			if ($elapsedDB > $this->sqlDebugTime || $debug) {
				$message = "dbExecute:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}	
		return $this->dbl->affected_rows();
	}
	/** dbInsert
	 * Вставляет новый элемент в таблицу
	 * @param type $table Название таблицы
	 * @param type $key_value_array Поле => Значение
	 * @param type $opt Опции
	 *	<br/><b>sqlString_spec</b> => true - Применяет функцию sqlString с опцией 'spec' и 10000
	 *	<br/><b>simple</b> => true - Оборачивает все значения в ковычки (преставляет в виде строки) 
	 *	<br/><b>not_simple_fields</b> => true - Поля исключения из опции simple
	 * @param type $debug Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return type affected_rows
	 */
	public function dbInsert($table, $key_value_array, $opt = array() ,$debug = false){
		$query_keys = '';
		$query_values = '';
		foreach($key_value_array as $key => $value) {
			if (isset($opt['sqlString_spec']) && $opt['sqlString_spec']){
				$value = $this->sqlString($value,(is_int($opt['sqlString_spec']))?$opt['sqlString_spec']:10000,'spec');
			}
			if (isset($opt['sqlString']) && $opt['sqlString']){
				$value = $this->sqlString($value,(is_int($opt['sqlString']))?$opt['sqlString']:10000);
			}
			if (isset($opt['simple']) && $opt['simple']) {
				if (isset($opt['not_simple_fields']) && in_array($key, $opt['not_simple_fields'])){
					// do nothing
				} else {
					$value = "'$value'";
				}
			}
			if ($query_keys) {
				$query_keys .= ', '.$key;
				$query_values .= ', '.$value;
			} else {
				$query_keys .= ' '.$key;
				$query_values .= ' '.$value;
			}
		}
		$query_string = ' INSERT INTO '.$table.' ('.$query_keys.') VALUES ('.$query_values.') ';
		$this->dbl->query($query_string);
		if ($debug || $this->sqlDebug) { 
			$elapsedDB = end($this->dbl->query_times);
			if ($elapsedDB > $this->sqlDebugTime || $debug) {
				$message = "dbInsert:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}	
		return $this->dbl->affected_rows();
	}
    
    public function dbSimpleInsert($table, $key_value_array){
        return $this->dbInsert($table, $key_value_array, [
            'simple' => true
        ]);
    }
    
	/** dbUpdate
	 * Изменяет поля в таблице
	 * @param type $table Название таблицы
	 * @param type $where_key_value_array Изменть Где Поле => Значение
	 * @param type $key_value_array Поле => Значение
	 * @param type $opt Опции
	 *	<br/><b>sqlString_spec</b> => true - Применяет функцию sqlString с опцией 'spec' и 10000
	 *	<br/><b>simple</b> => true - Оборачивает все значения в ковычки (преставляет в виде строки) 
	 *	<br/><b>not_simple_fields</b> => true - Поля исключения из опции simple
	 * @param type $debug Вывести Дебаг данные (в комментариях выводит запрос и время выполнения)
	 * @return type affected_rows
	 */
	public function dbUpdate($table, $where_key_value_array, $key_value_array, $opt = array(), $debug = false){
		$sqls_set_arr_str = array();
		foreach($key_value_array as $key => $value){
			if (isset($opt['sqlString_spec']) && $opt['sqlString_spec']){
				$value = $this->sqlString($value,(is_int($opt['sqlString_spec']))?$opt['sqlString_spec']:10000,'spec');
			}
			if (isset($opt['sqlString']) && $opt['sqlString']){
				$value = $this->sqlString($value,(is_int($opt['sqlString']))?$opt['sqlString']:10000);
			}
			if (isset($opt['simple']) && $opt['simple']) {
				if (isset($opt['not_simple_fields']) && in_array($key, $opt['not_simple_fields'])){
					// do nothing
				} else {
					$value = "'$value'";
				}
			}
			$sqls_set_arr_str[] = " $key = $value ";
			
		}
		if (!$sqls_set_arr_str) return 0;
		$sqls_set = implode(',',$sqls_set_arr_str);
		
		$sqls_where_arr_str = array();
		foreach($where_key_value_array as $key => $val){
			$sqls_where_arr_str[] = " $key = $val ";
		}
		if (!$sqls_where_arr_str){
			$sqls_where_arr_str = " id = 0 ";
		}
		$sqls_where = implode(' AND ',$sqls_where_arr_str);

		$this->dbl->query("UPDATE $table SET $sqls_set WHERE $sqls_where ");
		if ($debug || $this->sqlDebug) { 
			$elapsedDB = end($this->dbl->query_times);
			if ($elapsedDB > $this->sqlDebugTime || $debug) {
				$message = "dbUpdate:[sql_time=".number_format($elapsedDB, 6)."]\n::[".end($this->dbl->queries)."]";
				if ($this->sqlDebugPrint || $debug) {
					echo '<!--'.$message.'-->';
				}
				$this->log($message,array('file'=>'sql_debug.txt'));
			}
		}	
		return $this->dbl->affected_rows();
	}

    public function dbSimpleUpdate($table, $where_key_value_array, $key_value_array){
        return $this->dbUpdate($table, $where_key_value_array, $key_value_array, [
            'simple' => true
        ]);
    }
    
	
	/** dbStat
	 * Вывод информацию о Прошедших запросах в CodeIgniter
	 */
	public function dbStat(){
		echo '<!--';
		$this->debugParam($this->dbl->queries);
		$this->debugParam($this->dbl->query_times);
		echo '-->';
	}
	
	
	//****************************************
	//**** Работа с временной зоной в БД *****
	//****************************************
	/** $db_current_timezome
	 * Текущая Временная Зона в БД
	 * @var type 
	 */
	public $db_current_timezome = "@@session.time_zone";
	/** $db_store_timezone
	 * Временная Зона В которой будут Хранится DateTime
	 * @var type 
	 */
	public $db_store_timezone = "'+00:00'";
	/** $db_now
	 * Использовать вместо NOW() в запросах
	 * @var type 
	 */
	public $db_now = '';
	/** dbFromStore
	 * Обратное конвертирование даты в SQL
	 * @param type $field Поле для конвертации
	 * @param type $timezone Необходимая временная зона
	 * @return type Дата в необходимой временной зоне
	 */
	public function dbFromStore($field, $timezone = ''){
		if (!$timezone) $timezone = $this->db_current_timezome;
		return " CONVERT_TZ($field, ".$this->db_store_timezone.", ".$this->db_current_timezome.") ";
	}
	
	//************************************************************
	//**** Вспомогательные Функции в Отсутствии CodeIgniter  *****
	//************************************************************
	/** $load_tpl_root
	 * Дирректория расположения view  фалов
	 * @var type 
	 */
	public $load_tpl_root = '/application/views/';
	/** loadtpl
	 * Загрузка tpl для приложений на основе голого php
	 * @param type $tplname Имя файла tpl
	 * @param type $data Данные
	 * @return type контент
	 */
	public function loadtpl($tplname, $data) {
		extract($data);
		ob_start();
		include(rtrim($_SERVER['DOCUMENT_ROOT'],'/'). $this->load_tpl_root . $tplname . ".php");
		$buffer = ob_get_contents();
		@ob_end_clean();
		return $buffer;
	}
	/** sdbConnect
	 * Создание соединения с БД для приложений на основе голого php
	 * @param type $options
	 *	<br/><b>host</b> => Host
	 *	<br/><b>user</b> => user
	 *	<br/><b>password</b> => password
	 *	<br/><b>database</b> => database
	 *	<br/><b>port</b> => port
	 *	<br/><b>charset</b> => charset [utf8]
	 *	<br/><b>collation</b> => collation [utf8_general_ci]
	 * @return type connection or false
	 */
	public function sdbConnect($options = array()){
		if (!isset($options['host'])) return $this->setErrorMessage('parameter "host" is missing.');
		if (!isset($options['user'])) return $this->setErrorMessage('parameter "user" is missing.');
		if (!isset($options['password'])) return $this->setErrorMessage('parameter "password" is missing.');
		if (!isset($options['database'])) return $this->setErrorMessage('parameter "database" is missing.');
		$port = $this->g($options['port']);
		if ($port) $port = ':'.$port;
		$con = @mysql_connect($options['host'].$port, $options['user'], $options['password']);
		if (!$con) return $this->setErrorMessage('Unable to connect to the database');
		$selected = @mysql_select_db($options['database'], $con);
		if (!$selected) return $this->setErrorMessage('Unable to select database:'.$options['database']);
		if (!@mysql_query("SET NAMES '".$this->g($options['charset'],'utf8')."' COLLATE '".$this->g($options['collation'],'utf8_general_ci')."'", $con)){
			return $this->setErrorMessage('Unable to set charset');
		}
		return $con;
	}
	/** sdbExecute
	 * Выполнение запроса к БД для приложений на основе голого php
	 * @param type $sql Запрос
	 * @param type $conn Открытый коннекшн, иначе используется стандартный
	 * @return boolean affected_rows
	 */
	public function sdbExecute($sql,$conn = false){
		$result = ($conn)?mysql_query($sql,$conn):mysql_query($sql);
		if (!$result) {
			echo "Could not successfully run query ($sql) from DB: " . mysql_error();
			return false;
		}
		return ($conn)?mysql_affected_rows($conn):mysql_affected_rows();
	}
	/** sdbSelect
	 * Выборка массива записей из БД для приложений на основе голого php
	 * @param type $sql Запрос
	 * @param type $conn Открытый коннекшн, иначе используется стандартный
	 * @return mixed
	 */
	public function sdbSelect($sql,$conn = false){
		$result = ($conn)?mysql_query($sql,$conn):mysql_query($sql);
		if (!$result) {
			echo "Could not successfully run query ($sql) from DB: " . mysql_error();
			return false;
		}
		$ret = array();
		 while ($row = mysql_fetch_assoc($result)) {
			$rower = array();
			if (get_magic_quotes_runtime()){
				foreach ($row as $key => $value) {
					$rower[$key] = stripcslashes($value);
				}
			} else {
				$rower = $row;
			}
			$ret[] = $rower;
		}
		mysql_free_result($result);
		return $ret;
	}
	/** sdbSelectOne
	 * Выборка одной записи из БД для приложений на основе голого php
	 * @param type $sql Запрос
	 * @param type $conn Открытый коннекшн, иначе используется стандартный
	 * @return mixed
	 */
	public function sdbSelectOne($sql,$conn = false){
		$result = ($conn)?mysql_query($sql,$conn):mysql_query($sql);
		if(!$result) {
			echo "Could not successfully run query ($sql) from DB: " . mysql_error();
			return false;
		}
		if($row = mysql_fetch_assoc($result)) {
			if (get_magic_quotes_runtime()){
				foreach ($row as $key => $value) {
					$row[$key] = stripcslashes($row[$key]);
				}
			}
			mysql_free_result($result);
			return $row;
		}
		mysql_free_result($result);
		return false;
	}
	/**
	 * Выборка только поля одной записи из БД для приложений на основе голого php
	 * @param type $sql Запрос
	 * @param type $field Поле
	 * @param type $conn Открытый коннекшн, иначе используется стандартный
	 * @return mixed
	 */
	public function sdbSelectOneField($sql,$field,$conn = false){
		$result = ($conn)?mysql_query($sql,$conn):mysql_query($sql);
		if(!$result) {
			echo "Could not successfully run query ($sql) from DB: " . mysql_error();
			return false;
		}
		if($row = mysql_fetch_assoc($result)) {
			if (get_magic_quotes_runtime()){
				$ret = stripcslashes($row[$field]);
			} else {
				$ret = $row[$field];
			}
			mysql_free_result($result);
			return $ret;
		}
		mysql_free_result($result);
		return false;
	}
	
	//***********************
	//**** Логирование  *****
	//***********************
	/** $log_path
	 * Путь к файлу логов дефолтный
	 * @var string 
	 */
	public $log_path = '/pub/logs/loger.txt';
	public $log_dir = '/pub/logs/';
	public $log_maxsize = 5242880;
	/** log
	 * Логирование сообщения
	 * @param type $message Сообщение
	 * @param type $opt Опции
	 * <br/><b>open_type</b>-  Способ открытия файла По умолчанию 'a'
	 * <br/><b>file</b>-  Название файла
	 */
	public function log($message,$opt = array()){
		if(is_object($message) || is_array($message)){
            $message = print_r($message,true);
        } else {
            $message = (string) $message;
        }
		$filename = $this->log_path;
		if (isset($opt['file']) && $opt['file']){
			$filename = ((strpos($opt['file'],'/') === 0) ? '' : $this->log_dir) . $opt['file'];
		}
		$file = rtrim($_SERVER['DOCUMENT_ROOT'],'/').$filename;
		$fp = fopen($file, ((isset($opt['open_type']) && $opt['open_type']) ? $opt['open_type'] : 'a'));
		$timestring = '['.date('Y-m-d, H-i-s').']';
		$line = $timestring .' '. $message;
		fwrite($fp, $line ."\n");
		fclose($fp);
		if (filesize($file) > $this->log_maxsize || isset($opt['clear']) && $opt['clear']) {
			copy($file, $file.'-back-'.date('YmdHis').'.txt');
			unlink($file);
			$this->log('Log Cleared!',  array_merge($opt,array('open_type' => 'w')));
		}
	}
    private $clog_out = '';
	/**
	 * Лог в консоль
	 * @param type $message Сообщение
	 * @param type $clearLine Стереть строку (если только она была набрана без завершения строки)
	 * @param type $endLine Завершить строку
	 */
    public function clog($message, $clearLine = false, $endLine = true){
        if ($clearLine){
            print_r(str_pad('', strlen($this->clog_out), chr(0x08))); 
            print_r(str_pad('', strlen($this->clog_out), ' ')); 
            print_r(str_pad('', strlen($this->clog_out), chr(0x08))); 
            $this->clog_out = "";
        }
        if(is_object($message) || is_array($message)){
            $message = print_r($message,true);
        } else {
            $message = (string) $message;
        }
        $this->clog_out = $message; 
        print_r($message);   
        if ($endLine){
            print_r("\r\n");  
            $this->log($message,array('file' => 'console.txt'));
            $this->clog_out = "";
        }
    }
	
	//****************************************
	//**** Обработка данных для вставки  *****
	//****************************************	
	/** sqlInt
	 * Преобразует в int
	 * @param type $val Значение
	 * @param type $min Минимальное значение
	 * @param type $max Максимальное значение
	 * @return type
	 */
	public function sqlInt($val, $min = false, $max = false) {
		$val = intval($val);
		if (($min !== false) && ($val < $min)) $val = $min;
		if (($max !== false) && ($val > $max)) $val = $max;
		return $val;
	}
	/** sqlString
	 * Обрабатывает строку
	 * @param type $string Строка
	 * @param type $maxlen Максимальная длина строки в байтах UTF-8
	 * @param type $type Тип обработки
	 *	<br/><b>no_html</b> - trim, htmlspecialchars и mysql_real_escape_string
	 *	<br/><b>html</b> - Удаление скриптов и mysql_real_escape_string
	 *	<br/><b>html_noxss</b> - CI->xss_clean и mysql_real_escape_string
	 *	<br/><b>datetime</b> - YYYY-MM-DD HH:II:SS 
	 *	<br/><b>date</b> - YYYY-MM-DD
	 *	<br/><b>spec</b> - только mysql_real_escape_string 
	 *	<br/><b>none</b> - не обрабатывает никак
	 * @param type $opt
	 *	<br/><b>like</b> => true - Обрабатывает '%' для SQL LIKE
	 *	<br/><b>strip_tags</b> => true - Удаляет теги
	 *	<br/><b>n_to_br</b> => true - Конвертит \n и br в тег br
	 *	<br/><b>[b]_to_b</b> => true - Конвертит [b] в тег b и сохраняет b теги
	 *	<br/><b>[i]_to_i</b> => true - Конвертит [i] в тег i и сохраняет i теги
	 *	<br/><b>length_append</b> => true - Добавляет в конец строку, если строка была обрезена
	 * @return string
	 */
	public function sqlString($string, $maxlen = 255, $type = 'no_html',$opt = array()) {
		if (isset($opt['[b]_to_b']) && $opt['[b]_to_b']){
			$string = str_replace("<b>",'[b]',$string);
			$string = str_replace("</b>",'[/b]',$string);
			$string = str_replace("<strong>",'[b]',$string);
			$string = str_replace("</strong>",'[/b]',$string);
		}
		if (isset($opt['[i]_to_i']) && $opt['[i]_to_i']){
			$string = str_replace("<i>",'[i]',$string);
			$string = str_replace("</i>",'[/i]',$string);
		}
		if (isset($opt['n_to_br'])){
			$string = str_replace("<br>","\n",$string);
			$string = str_replace("<br/>","\n",$string);
			$string = str_replace("<br />","\n",$string);
		}
		if ($type === 'no_html') {
			$string = trim($string);
			$string = htmlspecialchars($string);
		} else if ($type === 'html') { // not recomended use
			$string = trim($string);
			$string = str_replace (array('<script>','</script>', 'javascript:',) , '', $string);
			//$string = str_replace ('=' , '&#61;', $string);
			//$string = str_replace ('"' , '&quot;', $string);
			if (isset($opt['save_classes'])){
				foreach($opt['save_classes'] as $class){
					$string = str_replace ('&#61;&quot;'.$class.'&quot;' , '="'.$class.'"', $string);
				}
			}
		} else if ($type === 'html_noxss'){
			$string = $this->CI->security->xss_clean($string);
		} else if (($type === 'datetime') || ($type === 'date')) {
			$string_arr = explode(' ', $string);
			$string_date_arr = explode('-',$string_arr[0]);
			$string_time_arr = explode(':',$this->g($string_arr[1]));
			$retYear = str_pad($this->sqlInt($string_date_arr[0],0,9999), 4, '0', STR_PAD_LEFT);
			$retMonth = str_pad($this->sqlInt($this->g($string_date_arr[1]),1,12), 2, '0', STR_PAD_LEFT);
			$retDay = str_pad($this->sqlInt($this->g($string_date_arr[2]),1,31), 2, '0', STR_PAD_LEFT);
			$retHour = str_pad($this->sqlInt($string_time_arr[0],0,24), 2, '0', STR_PAD_LEFT);
			$retMin = str_pad($this->sqlInt($this->g($string_time_arr[1]),0,60), 2, '0', STR_PAD_LEFT);
			$retSec = str_pad($this->sqlInt($this->g($string_time_arr[2]),0,60), 2, '0', STR_PAD_LEFT);
			$ret = $retYear.'-'.$retMonth.'-'.$retDay;
			if ($type === 'datetime'){ 
				$ret .= $add_str = ' '.$retHour.':'.$retMin.':'.$retSec;
			}
			return $ret;		
		} else if ($type === 'color') {
			$ret = '#'.str_pad(base_convert(base_convert(substr($string,1,6),16,10),10,16), 6, '0', STR_PAD_LEFT);
			return $ret;
		} if ($type == 'spec'){
			// do nothing :)
		}
		if (isset($opt['like'])){
			$string = str_replace(array('%', '_'), array('\\%', '\\_'), $string);
		}
		if(isset($opt['strip_tags'])){
			$string = strip_tags ($string);
		}
		if (isset($opt['n_to_br'])){
			$string = str_replace("\n",'<br/>',$string);
		}
		if (isset($opt['[b]_to_b']) && $opt['[b]_to_b']){
			$string = str_replace("[b]",'<b>',$string);
			$string = str_replace("[/b]",'</b>',$string);
		}
		if (isset($opt['[i]_to_i']) && $opt['[i]_to_i']){
			$string = str_replace("[i]",'<i>',$string);
			$string = str_replace("[/i]",'</i>',$string);
		}

		if($type !== 'none'){
			$string = mysqli_real_escape_string($this->dbl->conn_id,$string);
		}
		$source_len = mb_strlen($string);
		$string = mb_substr($string, 0, $maxlen);
		while (strlen($string) > $maxlen) {
			$cutter = $this->sqlInt((strlen($string)-$maxlen)/2,1);
			$string = mb_substr($string, 0,  mb_strlen($string) - $cutter);
		}
		$string = rtrim($string, '\\');
		if (isset($opt['length_append']) && mb_strlen($string) < $source_len){
			$string .= $opt['length_append'];
		}
		return $string;
	}	
	private function mres($value)
	{
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

		return str_replace($search, $replace, $value);
	}
	
	public function alias($str){
		$ret = $this->translit($str, 'ru-en', true);
		$ret = preg_replace("/\s+/",'-',$ret);
		$ret = $this->replacer($ret,array(),array('kill_badchars' => true));
		$ret = preg_replace("/\-+/",'-',$ret);
		return $ret;
	}

	/** validEmail
	 * Проверяет валидность E-mail
	 * @param type $email Строка E-mail
	 * @return booleam
	 */
	public function validEmail($email){
		return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
	}	

	//****************************************
	//**** Всмпомогательные функции      *****
	//****************************************		
	/** firstTrue
	 * Выдаёт первый не "false"ный элементы
	 * @param type $str_arr Массив элементов
	 * @return string
	 */
	public function firstTrue($str_arr){
		foreach($str_arr as $str){
			if ($str) return $str;
		}
		return '';
	}
	/** isAjax
	 * Сделан ли данный запрсо Аяксом
	 * @return boolean
	 */
	public function isAjax(){
		return (
			isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
		) ? true : false;
	}
	
	//****************************************
	//**** MJSA функции                  *****
	//****************************************	
	/** $request_type
	 * На какиз апросы отвечает контроллер
	 * mjsa_ajax или simple по умочланию
	 * @var type 
	 */
	public $request_type = 'simple';
	/** location
	 * Редиректит на другю страницу в зависимости от запроса
	 * @param type $url
	 * @param type $code
	 * @return boolean
	 */
	public function location($url, $code = false){
		if($this->request_type === 'mjsa_ajax' || $this->g($_GET['body_ajax'])){
			echo '<mjsa_separator/><stop_separator/><redirect_separator/>'.$url.'<redirect_separator/>';
		} else {
			if($code === 301){
				header("HTTP/1.0 301 Moved Permanently");
			}
			if($code === 302){
				header("HTTP/1.0 302 Moved Temporarily");
			}
			header('Location: '.$url);
		}
		return false;
	}
	/** mjsaInsert
	 * Вставляет контент в указаную дивку
	 * @param type $selector css  селектор куда вставить блок
	 * @param type $content Содержимое блока
	 * @param type $opt - Опции (их нет пока)
	 * @return type Возвращает строку для echo
	 */
	public function mjsaInsert($selector,$content,$opt = array()){
		return '<mjsa_separator/><noservice_separator/>'
				.'<html_replace_separator/>'.$selector.'<html_replace_to/>'
				. $content
				.'<html_replace_separator/>'
				.'<noservice_separator/>'; 
	}		
	public function mjsaPrintInsert($selector,$content,$opt = array()){
		echo $this->mjsaInsert($selector,$content,$opt);
		return false;
	}		
	
	/** mjsaAppend
	 * Вставляет контент в указаную дивку в конец
	 * @param type $selector css  селектор куда вставить блок
	 * @param type $content Содержимое блока
	 * @param type $opt - Опции (их нет пока)
	 * @return type Возвращает строку для echo
	 */
	public function mjsaAppend($selector,$content,$opt = array()){
		return '<mjsa_separator/><noservice_separator/>'
				.'<html_append_separator/>'.$selector.'<html_append_to/>'
				. $content
				.'<html_append_separator/>'
				.'<noservice_separator/>'; 
	}		
	public function mjsaPrintAppend($selector,$content,$opt = array()){
		echo $this->mjsaAppend($selector,$content,$opt);
		return false;
	}		
	/** mjsaEvent
	 * Манипулирует mjsa событиями 
	 * @param type $events
	 * <br/> <b>success</b>Выводит стандартное уведомление успешного сообщения
	 * <br/> <b>error</b>Выводит стандартное уведомление сообщения ошибки
	 * <br/> <b>incorrect</b> доставляет incorrect_separator, для индикации поля ошибки в форме
	 * <br/> <b>redirect</b>Редиректит на другую страницу
	 * <br/> <b>stop</b>Останавливает вставку контента
	 * <br/> <b>reload</b>Обновляет страницу
	 * <br/> <b>closePopups</b>Закрывает открытые попапы
	 * <br/> <b>customs</b>Дополнительные кастомные теги
	 * @param type $opt Опции (не используется)
	 * @return string
	 */
	public function mjsaEvent($events,$opt = array()){
		$ret = '';
		if (isset($events['success'])){
			$ret .= '<success_separator/>'.$events['success'].'<success_separator/>';
		}
		if (isset($events['error'])){
			$ret .= '<error_separator/>'.$events['error'].'<error_separator/>';
		}
		if (isset($events['customs']) && is_array($events['customs'])){
			foreach($events['customs'] as $custom => $value){
				$ret .= $custom.''.$value.''.$custom;
			}
		}
		if (isset($events['incorrect'])){
			$ret .= '<incorrect_separator/>'.$events['incorrect'].'<incorrect_separator/>';
		}
		if (isset($events['redirect'])){
			$ret .= '<redirect_separator/>'.$events['redirect'].'<redirect_separator/>';
		}
		if (isset($events['stop'])){
			$ret .= '<stop_separator/>'.$events['stop'].'<stop_separator/>';
		}
		if (isset($events['reload'])){
			$ret .= '<script>mjsa.bodyAjaxUpdate();</script>';
		}
		if (isset($events['closePopups'])){
			$ret .= '<script>mjsa.scrollPopup.closeAll();</script>';
		}
		if ($ret){
			$ret = '<mjsa_separator/>'.$ret;
		}
		return $ret;		
	}
	
	/** mjsaError
	 * Выводит сообщение об ошибке
	 * @param type $error 
	 * Сообщение об ошибке
	 * <br/> После знака "|" может быть json с доп. аттрибутами
	 * <br/> Теже данные что в <b>mjsaEvent</b>
	 * @param type $opt Опции
	 * <br/> <b>custom_error</b> - Сепаратор отличный от error_separator иcпользуемый в mjsa поумолчанию для ошибок в всплывающей посказке
	 * <br/> <b>events</b> - Передаются дополнительные События
	 * @return string
	 */
	public function mjsaError($error,$opt = array()){
		$events = null;
		if (strpos($error,'|') !== false) {
			$error_arr = explode('|',$error);
			$error = $error_arr[0];
			if (isset($error_arr[1])){
				$events = @json_decode($error_arr[1],true);
			}
		} 
		if (!$events) $events = array();
		if (isset($opt['events'])){
			$events = array_merge($events,$opt['events']);
		}
		if(isset($opt['custom_error'])){
			return $this->mjsaEvent(
					array_merge(
						array('customs' => array($opt['custom_error'] => $error)),
							$events
					)
				);
		} else {
			return $this->mjsaEvent(
					array_merge(
						array('error' => $error),
						$events
					)
				);
		}
	}
	/** mjsaSuccess
	 * Выводит  уведомление успешного сообщения
	 * @param type $message 
	 * Уведомление
	 * <br/> После знака "|" может быть json с доп. аттрибутами
	 * <br/> Теже данные что в <b>mjsaEvent</b>
	 * @param type $opt Опции
	 * <br/> <b>custom_success</b> - Сепаратор отличный от success_separator иcпользуемый в mjsa поумолчанию для ошибок в всплывающей посказке
	 * <br/> <b>events</b> - Передаются дополнительные События
	 * @return string
	 */
	public function mjsaSuccess($message,$opt = array()){
		$events = null;
		if (strpos($message,'|') !== false) {
			$error_arr = explode('|',$message);
			$message = $error_arr[0];
			if (isset($error_arr[1])){
				$events = @json_decode($error_arr[1],true);
			}
		} 
		if (!$events) $events = array();
		if (isset($opt['events'])){
			$events = array_merge($events,$opt['events']);
		}
		if(isset($opt['custom_success'])){
			return $this->mjsaEvent(
					array_merge(
						array('customs' => array($opt['custom_success'] => $message)),
							$events
					)
				);
		} else {
			return $this->mjsaEvent(
					array_merge(
						array('success' => $message),
						$events
					)
				);
		}		
	}
	/** mjsaPrintEvent 
	 * mjsaEvent с выводом
	 */
	public function mjsaPrintEvent($events,$opt = array()){
		echo $this->mjsaEvent($events,$opt);
		return false;
	}
	/** mjsaPrintError
	 * mjsaError с выводом
	 */
	public function mjsaPrintError($error,$opt = array()){
		echo $this->mjsaError($error,$opt);
		return false;
	}
	/** mjsaPrintSuccess
	 * mjsaSuccess с выводом
	 */
	public function mjsaPrintSuccess($message,$opt = array()){
		echo $this->mjsaSuccess($message,$opt);
		return false;
	}
	/** mjsaValidator
	 * Валидирует входные данные и отправляет mjsa ошибку
	 * [TODO] Будет дописываться
	 * @param type $input Массив $_POST  или другой массив в котором могут находится данные
	 * @param type $config Массив настроек для входных данных
	 * @param type $opt - передаются в mjsaPrintError дял кастомного отображения ошибок
	 * @return array Необходимые входные данные или false  в случае ошибки входных данных
	 */
	public function mjsaValidator($input, $config, $opt = array()){
		$ret = array();
		foreach($config as $field => $field_opts){
			if($this->g($field_opts['required']) && trim($this->g($input[$field])) === ''){
				$this->setErrorMessage($field);
				return $this->mjsaPrintError(
						$this->g($field_opts['required_text'],lang('mm_verification_required'))
						.'|{"incorrect":"'.$field.'"}'
					,$opt);
			}
			if (!isset($input[$field])) {
				$ret[$field] = false;
				continue;
			}
			if ($this->g($input[$field]) === '' && !$this->g($field_opts['required'])){
				$ret[$field] = $this->g($input[$field]);
				continue;
			}
			if($this->g($field_opts['max_length']) && $field_opts['max_length'] < strlen($this->g($input[$field]))){
				$this->setErrorMessage($field);
				return $this->mjsaPrintError(
						$this->g($field_opts['max_length_text'],lang('mm_verification_max_length'))
						.'|{"incorrect":"'.$field.'"}'
					,$opt);
			}
			if($this->g($field_opts['email']) && !$this->validEmail($this->g($input[$field]))){
				$this->setErrorMessage($field);
				return $this->mjsaPrintError(
						$this->g($field_opts['email_text'],lang('mm_verification_email'))
						.'|{"incorrect":"'.$field.'"}'
					,$opt);
			}
			if($this->g($field_opts['phone_easy']) && (intval(strtr($input[$field],array(' '=>'','+'=>'','-'=>'',"\t"=>'','('=>'',')'=>''))) < 1000000)){
				$this->setErrorMessage($field);
				return $this->mjsaPrintError(
						$this->g($field_opts['phone_easy_text'],lang('mm_verification_phone'))
						.'|{"incorrect":"'.$field.'"}'
					,$opt);
			}
			if($this->g($field_opts['phone']) && $this->g($field_opts['length']) && strlen(preg_replace('%[^0-9]%', '', $input[$field])) !== $field_opts['length']){
				$this->setErrorMessage($field);
				return $this->mjsaPrintError(
						$this->g($field_opts['phone_text'],lang('mm_verification_phone'))
						.'|{"incorrect":"'.$field.'"}'
					,$opt);
			}
			$ret[$field] = trim($this->g($input[$field]));
		}
		return $ret;
	}	
	
	//****************************************
	//**** MOP Функции                   *****
	//****************************************		
	/** readFileVersion
	 * Читает версию файла и дату последнего изменения
	 * @param type $file Путь от корня сайта
	 * @return type array('ver'=>'?', 'lastmod'=>'?')
	 */
	public function readFileVersion($file){
		$bdir = rtrim($_SERVER['DOCUMENT_ROOT'],'/');
		$path = $bdir . $file;
		if (file_exists($path) && is_file($path)){
			$fcontent = file_get_contents($path); //readfile
			preg_match("/(v[0-9]+\.[0-9]+[^\s]*)/i",$fcontent,$res);
			return array('ver' => $this->g($res[0]), 'lastmod' => $this->date('Y-m-d H:i',array('time' => filemtime($path))));
		} else {
			return array('ver' => 'file not found!', 'lastmod' => 'file not found!');
		}
	}	
	/** getMopVersion
	 * Читает версию Архитектуры Mades Online Pattern сайта
	 * @param type $file Путь от корня сайта
	 * @return type array('ver'=>'?', 'lastmod'=>'?')
	 */
	public function getMopVersion(){
		$info = $this->readFileVersion(APPPATH . 'controllers/mop/mcontroller.php');
		return $info['ver'];
	}
	/** copyright
	 * Возвращает данные копирайта
	 * @param type $obj объект контроллера или вью
	 * @param type $field Поле для возврата
	 * @return string
	 */
	public function copyright(&$obj,$field){
		if (isset($obj->data['copyright']) && isset($obj->data['copyright'][$field])){
			return $obj->data['copyright'][$field];
		} else if (isset($obj[$field])) {
			return $obj[$field];
		} else {
			return 'undefined';
		}
	}	
	public $useu = 'aHR0cDovL2JvZ2FyZXZpY2guY29tL21vcC9tb3BhcGkvZG9tdXNl';
	public $tsignature = '8dc66b2be10c6882c4565f74a2f9f21f'; 
	
	public function useAction(){
		if (strpos(@$_SERVER['REQUEST_URI'],'api') !== false) return true;
		$usetime = intval($this->getSiteParam('use_time'));
		$time = time();
		if (!($usetime + 86400 < time() || $usetime > $time)) return true;
		$resp = $this->curlRequest(base64_decode($this->useu).'?domain='.urlencode(@$_SERVER['HTTP_HOST']).'&'.'license='.urlencode($this->g($this->license)));
		if ($resp == 'ok') return false;
		$this->setSiteParam('use_time',$time);
		return true;
	}
	public $tmode = true; // enable gui administrate
	
	/** getSiteParam
	 * Получение переменных для сайта [таблица site_params] 
	 * @param type $key
	 * @param type $default_string false  выдавать false  если переменной нету. Иначе выдатся строка вида [siteParam:$key]
	 * @return type
	 */
	public function getSiteParam($key, $default_string = true){
		$key = $this->sqlString($key,255,'spec');
		$ret = $this->dbSelectOneField("SELECT the_value FROM site_params WHERE the_key = '$key' ",'the_value');
		if ($ret === false && $default_string) {
			$ret = "[siteParam:$key]";
		}
		return $ret;
	}
	/** setSiteParam
	 * Установка переменных для сайта [таблица site_params] 
	 * @param type $key
	 * @param type $value
	 * @return type
	 */
	public function setSiteParam($key,$value){
		$key = $this->sqlString($key,255,'spec');
		$value = $this->sqlString($value,65000,'spec');
		if ($this->dbSelectOneField("SELECT the_value FROM site_params WHERE the_key = '$key' ",'the_value')!==false){
			return $this->dbExecute("UPDATE site_params SET the_value = '$value' WHERE the_key = '$key' ");
		}else {
			return $this->dbExecute("INSERT INTO site_params (the_key, the_value) VALUES ('$key','$value')");
		}
	}
	/**
	 * Получает SEO  данные
	 * @param type $table_name
	 * @param type $primary_value
	 * @return type
	 */
	public function getSeo($table_name,$primary_value){
		$table_name = $this->sqlString($table_name,63,'spec');
		$primary_value = $this->sqlString($primary_value,255,'spec');
		return $this->dbSelectOne("SELECT * FROM site_seo WHERE table_name = '$table_name' AND primary_value = '$primary_value'");
	}
	/**
	 * Отсылает Email  от имени сайта
	 * @param type $email
	 * @param type $subject
	 * @param type $textPattern
	 * @param type $patternReplace
	 * @return boolean
	 */
	public function sendEmail($email, $subject, $textPattern, $patternReplace) {
		$this->load->library('email');
		foreach($patternReplace as $key => $value) {
			$textPattern = str_replace($key,$value,$textPattern);
		}
		$email_config = array();
		$email_config['mailtype'] = 'text';
		$email_config['protocol'] = $this->g($this->app_properties['site_email_protocol'],'sendmail');
		$this->email->initialize($email_config);
		$this->email->from(
				$this->app_properties['site_robot_email'], 
				$this->app_properties['site_robot_name']
				);
		$this->email->to($email); 
		$this->email->subject($subject);
		$this->email->message($textPattern);
		
		if (!$this->email->send()) {
			$this->log($this->email->print_debugger());
			return $this->setErrorMessage(lang('mauth.cant_send_email'));
		}
		$this->last_message = $this->email->print_debugger();	
		$this->log('Sending email to '.$email. ' Subject: '.$subject);
		return true;
	}
	/**
	 * Возвращаяет SQL LIMIT  в зависимости от параметров
	 * @param type $opt
	 * <br/> count != all - лимитирует
	 * <br/> from - откуда
	 * <br/> page, per_page - отпределённая страница
	 * @return type
	 */
	public function optToSqlLimit($opt){
		$sql_limit = ''; $from = 0; $count = 'all';
		if (isset($opt['count']) && $opt['count'] && $opt['count'] !== 'all' ) $count = $this->sqlInt($opt['count'],0);
		if (isset($opt['from']) && $opt['from']) $from = $this->sqlInt($opt['from'],0);
		if (isset($opt['page']) && $opt['page'] && isset($opt['per_page']) && $opt['per_page']){
			$from = ($opt['page']-1)*$opt['per_page'];
			$count = $opt['per_page'];
		}
		if ($count !== 'all') $sql_limit = " LIMIT $from, $count ";
		return $sql_limit;
	}
	
	//****************************************
	//**** Вспомогательные функции       *****
	//****************************************			
	/** secondsToTimeSimple
	 * Получение интервала в удобном виде количество дней, часов, минут, секунд
	 * @param type $seconds
	 * @return array $times:
	 * $times[0] - секунды
	 * $times[1] - минуты
	 * $times[2] - часы
	 * $times[3] - дни
	 * $times[4] - года
	 */
	public function secondsToTimeSimple($seconds){
		$bchange = false;
		if ($seconds < 0){ $bchange = true; $seconds = - $seconds; }
		$times = array(0,0,0,0,0);
		$periods = array(60, 3600, 86400, 31536000);
		for ($i = 3; $i >= 0; $i--){
			$period = floor($seconds/$periods[$i]);
			if (($period > 0)) {
				$times[$i+1] = $period; if ($bchange) $times[$i+1] = - $times[$i+1];
				$seconds -= $period * $periods[$i];
			}
		}
		if ($seconds >= 0){
			$times[0] = $seconds; $times[0] = - $times[0];
		}
		return $times;
	}	
	public function sqlTime($time){
		return $this->date('Y-m-d H:i:s',[
			'time' => $time,
			'timezone_name' => 'UTC',
		]);
	}

	/** date
	 * Форматирвоание даты в зависимости от временой зоны, локали и т.д.
	 * @param type $format Формат вывода даты
	 * @param type $opt Опции и входные параметры
	 * <br/><b>time</b> Время в формате timestamp
	 * <br/><b>string</b> Время в формате SQL DateTime 
	 * <br/><b>string_timezone</b> SQL DateTime Временная зона
	 * <br/>Если не указаны ни одно ни другое берётся текущее time()
	 * <br/><b>timezone_name</b> Временная зона вывода
	 * <br/>Иначе берётся site_timezone из параметрво сайта
	 * <br/><b>return_timestamp</b> Возвращает неотформатированный timestamp
	 * <br/><b>format</b> => true  использует strftime иначе date
	 * format
	 * @param type $ui  UI  пользователя $ui['timezone']- смещение локали в минутах относительно UTC
	 * @return type
	 */
	public function date($format, $opt = array(), $ui = array()){
		if(isset($this->app_properties['format_locale'])){
			setlocale(LC_TIME, $this->app_properties['format_locale'].'.UTF8');
		}
        if (!is_array($opt) && is_numeric($opt)) {
            $opt = ['time' => $opt];
        }

		if (true || !class_exists('DateTime')) { // date // php 5.2 and less
			$time = time();
			if(isset($opt['time'])){
				$time = $opt['time'];
			}
			if(isset($opt['string'])){
				date_default_timezone_set('UTC');
				if(isset($opt['string_timezone'])){
					date_default_timezone_set($opt['string_timezone']);
				}
				$time = strtotime($opt['string']);
			}
			if (isset($opt['timezone_name'])){
				date_default_timezone_set($opt['timezone_name']);
			} else if($ui && isset($ui['timezone']) && $ui['timezone'] !== ''){
				date_default_timezone_set('UTC');
				$time -= intval($ui['timezone'])*60;
				if (isset($opt['utc_append'])){
					$times = $this->secondsToTimeSimple(-$ui['timezone']*60);
					$utc = ' UTC';
					$utc .= ($times[2]>0)?'+'.$times[2]:'-'.abs($times[2]);
					$utc .= ($times[1])?':'.abs($times[1]):'';
				}
			} else if (isset($opt['return_timestamp'])){
				return $time;
			} else {
				date_default_timezone_set($this->app_properties['site_timezone']);
			}
			if (isset($opt['format']) && $opt['format']){
				$date = strftime($format,$time);
			} else {
				$date = date($format,$time);
			}
			if (isset($opt['utc_append']) && isset($utc)){
				$date .= $utc;
			}
			return $date;
		} else { // DateTime // php 5.3 and more
			// TODO
		}
		//$this->app_properties['site_timezone'];
	}
	/** wordEnd
	 * Вставляет слово со склонением в зависимости от числа
	 * @param type $count Число
	 * @param type $word1 Слово для числа 1
	 * @param type $word2 Слово для числа 2
	 * @param type $word5 Слово для числа 5
	 * @return type
	 */
	public function wordEnd($count, $word1, $word2, $word5) {
		$num = intval($count)%100;
		if ($count>19) { $num=$num%10; }
		switch ($num) {
			case 1:  { return($word1); }
			case 2: case 3: case 4:  { return($word2); }
			default: { return($word5); }
		}
	}
	public function fillWordEnd($count,$word125String,$fillCount = true) {
		$word125Arr = explode(';',$word125String);
		if (!isset($word125Arr[1])) $word125Arr[1] = $word125Arr[0];
		if (!isset($word125Arr[2])) $word125Arr[2] = $word125Arr[0];
		return ((($fillCount) ? $count . ' '  : ' ') 
				. $this->wordEnd(intval($count), $word125Arr[0], $word125Arr[1], $word125Arr[2]));
	}

	/** jsonEncode
	 * Кодирует в json, в том числе кирилицу, даже если сервер не поддерживает JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE
	 * @param type $param
	 * @return type
	 */
	public function jsonEncode($param) {
		if (defined('JSON_UNESCAPED_SLASHES') && defined('JSON_UNESCAPED_UNICODE')){
			return json_encode($param, JSON_UNESCAPED_SLASHES ^ JSON_UNESCAPED_UNICODE); 
		} else {
			array_walk_recursive($param, array($this, 'jsonWalkFunction'));
            return mb_decode_numericentity(json_encode($param), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
		}
	}
	private function jsonWalkFunction(&$item, $key) {
        if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
    }
	/** mDateConvert
	 * Конвертирует даты между видами
	 * <br/> m : 2014-05-22
	 * <br/> en : 05/22/2014
	 * <br/> ru : 22.05.2014
	 * @param type $input Входная дата
	 * @param type $type  m-en, m-ru, ru-m, en-m, ru-en, en-ru
	 * @return string
	 */
	public function mDateConvert($input,$type = 'm-en') {
		$type_arr = explode('-', $type);
		if (count($type_arr) != 2) {
			return 'convert type error!';
		}
		$from = $type_arr[0];
		$to = $type_arr[1];
		$m_date = $input;
		if ($from === 'en') {
			$date_arr = explode('/', $input);
			if (count($date_arr) != 3) { return 'Incorrect date!'; }
			$m_date = str_pad(intval($date_arr[2]), 4, '0', STR_PAD_LEFT).'-'
				.str_pad(intval($date_arr[0]), 2, '0', STR_PAD_LEFT).'-'
				.str_pad(intval($date_arr[1]), 2, '0', STR_PAD_LEFT);
		}
		if ($from === 'ru') {
			$date_arr = explode('.', $input);
			if (count($date_arr) != 3) { return 'Incorrect date!'; }
			$m_date = str_pad(intval($date_arr[2]), 4, '0', STR_PAD_LEFT).'-'
				.str_pad(intval($date_arr[1]), 2, '0', STR_PAD_LEFT).'-'
				.str_pad(intval($date_arr[0]), 2, '0', STR_PAD_LEFT);
		}
		$ret_date = $m_date;
		$date_arr = explode('-', $m_date);
		if ($to === 'en') {
			$ret_date = str_pad(intval($date_arr[1]), 2, '0', STR_PAD_LEFT).'/'
				.str_pad(intval($date_arr[2]), 2, '0', STR_PAD_LEFT).'/'
				.intval($date_arr[0]);
		}
		if ($to === 'ru') {
			$ret_date = str_pad(intval($date_arr[2]), 2, '0', STR_PAD_LEFT).'.'
				.str_pad(intval($date_arr[1]), 2, '0', STR_PAD_LEFT).'.'
				.intval($date_arr[0]);
		}	
		return $ret_date;
	}
	/** keymapEnRu
	 * Изменяет раскадку строки, если неправильно введено
	 * @param string $str
	 * @return string
	 */
	public function keymapEnRu($str){
		$ret = strtolower($str);
		$ret= $this->replacer($ret, array(
			'q' => 'й', 'w' => 'ц', 'e' => 'у', 'r' => 'к', 't' => 'е', 'y' => 'н', 'u' => 'г', 'i' => 'ш', 'o' => 'щ', 'p' => 'з', '[' => 'х', ']' => 'ъ',
			'a' => 'ф', 's' => 'ы', 'd' => 'в', 'f' => 'а', 'g' => 'п', 'h' => 'р', 'j' => 'о', 'k' => 'л', 'l' => 'д', ';' => 'ж', '\'' => 'э',
			'z' => 'я', 'x' => 'ч', 'c' => 'с', 'v' => 'м', 'b' => 'и', 'n' => 'т', 'm' => 'ь', ',' => 'б', '.' => 'ю', '/' => '.',  '`' => 'ё',
			'й' => 'q', 'ц' => 'w', 'у' => 'e', 'к' => 'r', 'е' => 't', 'н' => 'y', 'г' => 'u', 'ш' => 'i', 'щ' => 'o', 'з' => 'p', 'х' => '[', 'ъ' => ']',
			'ф' => 'a', 'ы' => 's', 'в' => 'd', 'а' => 'f', 'п' => 'g', 'р' => 'h', 'о' => 'j', 'л' => 'k', 'д' => 'l', 'ж' => ';', 'э' => '\'',
			'я' => 'z', 'ч' => 'x', 'с' => 'c', 'м' => 'v', 'и' => 'b', 'т' => 'n', 'ь' => 'm', 'б' => ',', 'ю' => '.', '.' => '/', 'ё' => '`'
			)
		);
		return $ret;
	}
	/** translit
	 * функция превода текста с кириллицы в траскрипт
	 * @param type $st строка
	 * @param type $type [ru-en][en-ru]
	 * @param type $inlower => true - приводит в нижний регистр
	 * @param type $method По-умолчанию новый метод перевода
	 * @return type
	 */
	public function translit($st, $type = 'ru-en', $inlower = false, $method = 'm'){
		if ($method === 'm') {
			if ($type === 'ru-en') {
				$st=$this->mbStrtr($st,'абвгдеёзийклмнопрстуфхъыэ', 'abvgdeeziyklmnoprstufh\'iei');
				$st=$this->mbStrtr($st,'АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ', 'ABVGDEEZIYKLMNOPRSTUFH\'IEI');
				$st=$this->replacer($st, 
					array('ж'=>'zh', 'ц'=>'ts', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shch',
					'ь'=>'', 'ю'=>'yu', 'я'=>'ya', 'Ж'=>'ZH', 'Ц'=>'TS',
					'Ч'=>'CH', 'Ш'=>'SH', 'Щ'=>'SHCH','Ь'=>'', 'Ю'=>'YU',
					'Я'=>'YA', 'ї'=>'i', 'Ї'=>'Yi', 'є'=>'ie', 'Є'=>'Ye',
					'É' => 'E', 'é' => 'e','Ê' => 'E', 'ê' => 'e',
					'È' => 'E', 'è' => 'e','Ë' => 'E', 'ë' => 'e',
					'Â' => 'A', 'â' => 'a','À' => 'A', 'à' => 'a',
					'Ç' => 'C', 'ç' => 'c','Ô' => 'O', 'ô' => 'o',
					'Î' => 'I', 'î' => 'i','Ï' => 'I', 'ï' => 'i',
					'Û' => 'U', 'û' => 'u','Ù' => 'U', 'ù' => 'u',
					'Ü' => 'U', 'ü' => 'u','Ÿ' => 'Y', 'ÿ' => 'y',
				));
			} else {
				$st=$this->replacer($st, 
					array('zh'=>'ж', 'ts'=>'ц', 'ch'=>'ч', 'sh'=>'ш', 'shch'=>'щ',
					'yu'=>'ю', 'ya'=>'я', 'ZH'=>'Ж', 'TS'=>'Ц',
					'CH'=>'Ч', 'SH'=>'Ш', 'SHCH'=>'Щ', 'YU'=>'Ю',
					'YA'=>'Я', 'i'=>'ї', 'Yi'=>'Ї', 'ie'=>'є', 'Ye'=>'Є'));
				$st=$this->mbStrtr($st,'abvgdeeziyklmnoprstufh\'iei', 'абвгдеёзийклмнопрстуфхъыэ');
				$st=$this->mbStrtr($st,'ABVGDEEZIYKLMNOPRSTUFH\'IEI', 'АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ');

			}
		} else {
			if ($type === 'ru-en') {
				$st=strtr($st,"абвгдеёзийклмнопрстуфхъыэ", "abvgdeezijklmnoprstufh'iei");
				$st=strtr($st,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ", "abvgdeezijklmnoprstufh'iei");
				$st=strtr($st, 
					array("ж"=>"zh", "ц"=>"ts", "ч"=>"ch", "ш"=>"sh", "щ"=>"shch",
					"ь"=>"", "ю"=>"yu", "я"=>"ya", "Ж"=>"zh", "Ц"=>"ts", 
					"Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shch","Ь"=>"", "Ю"=>"yu", 
					"Я"=>"ya", "ї"=>"i", "Ї"=>"Yi", "є"=>"ie", "Є"=>"Ye"));
			} else {
				//[edit]
			}
		}
		if ($inlower) $st = mb_strtolower($st);
		return $st;
	}
	private function mbStrtr($str, $from, $to){
	  return str_replace($this->mbStrSplit($from), $this->mbStrSplit($to), $str);
	}
	private function mbStrSplit($str) {
		return preg_split('~~u', $str, null, PREG_SPLIT_NO_EMPTY);;
	}
	/** replacer
	 * Подменяет подстроки в строке
	 * @param type $textPattern
	 * @param type $patternReplace
	 * @param type $opt
	 * <br/><b>kill_badchars</b> => true  удаляет все левые символы и русские
	 * <br/><b>kill_badchars_ru</b> => true  удаляет все левые символы русские оставляет
	 * @return type
	 */
	public function replacer($textPattern, $patternReplace = array(),$opt = array()){
		foreach($patternReplace as $key => $value) {
			$textPattern = str_replace($key,$value,$textPattern);
		}
		if (isset($opt['kill_badchars'])){
			$textPattern = preg_replace('%[^A-Za-z0-9_-]%', '', $textPattern);
		}
		if (isset($opt['kill_badchars_ru'])){
			$textPattern = preg_replace('%[^A-Za-zА-Яа-я0-9_-]%', '', $textPattern);
		}
		return $textPattern;
	}
	/** stringAllow
	 * [TODO] фильтрует строку по разрешённым символам
	 * @param type $st
	 * @param type $allowChars
	 */
	public function stringAllow($st, $allowChars = 'a-zA-Z0-9'){
		return preg_replace("/[^$allowChars]+/", "", $st);
	}
	
	/**
	 * Фунция переводящая любую строку в пригодную для alias
	 * @param type $text
	 * @return type
	 */
	public function getAliasFromText($text){
		$ret = $this->translit($text, 'ru-en', true);
		$ret = preg_replace("/\s+/",'-',$ret);
		$ret = $this->replacer($ret,array(),array('kill_badchars' => true));
		$ret = preg_replace("/\-+/",'-',$ret);
		return $ret;
	}
	
	/** curlRedirExec
	 * Внетренняя функция для скачки файлов через редиректы
	 */
	private function curlRedirExec($ch) {  
		static $curl_loops = 0;  
		static $curl_max_loops = 20;  
		if ($curl_loops   >= $curl_max_loops) { $curl_loops = 0; return $this->setErrorMessage('Error: Too many redirects!!!'); }
		curl_setopt($ch, CURLOPT_HEADER, true);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		$data = curl_exec($ch);  
		if (!$data){
			usleep(2000000); $data = curl_exec($ch); 
		}
		if (!$data){
			usleep(2000000); $data = curl_exec($ch); 
		}
		if (count(explode("\r\n\r\n", $data)) < 2){
			return $this->setErrorMessage('Cant download file');
		}
		list($header, $data) = explode("\r\n\r\n", $data, 2);  
		$this->lastResponseInfo['info'] = curl_getinfo($ch);
		$this->lastResponseInfo['header'] = $header;

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
		if ($http_code == 301 || $http_code == 302) {  
			$matches = array();  
			preg_match('/Location:[ ]*([^[\s\n\t\r]*]*)/', $header, $matches);
			$url_location = trim(array_pop($matches));
			if (strpos($url_location,'//') === 0) { $url_location = 'http:'.$url_location; }
			$url = @parse_url($url_location); 
			if (!$url) {  
				//couldn't process the url to redirect to  
				$curl_loops = 0;  
				return $data;  
			}  
			$last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));  
			if (!$url['scheme'])  
				$url['scheme'] = $last_url['scheme'];  
			if (!$url['host'])  
				$url['host'] = $last_url['host'];  
			if (!$url['path'])  
				$url['path'] = $last_url['path'];  
			$new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . (@$url['query']?'?'.$url['query']:'');  
			//echo 'url=['.$new_url.']';
			curl_setopt($ch, CURLOPT_URL, $new_url);  
			//debug('Redirecting to', $new_url);
			$curl_loops++;
			return $this->curlRedirExec($ch);  
		} else {  
			$curl_loops=0;  
			return $data;
		}
	}  
	/** curlGrabFile
	 * Скачивает файл во временную дирректорию. 
	 * @param type $url URL  файла
	 * @param string $tempDir Временная дирректория './pub/files/temp/'
	 * @return string $filename Путь к скаченому файлу
	 */
	public function curlGrabFile($url, $tempDir = './pub/files/temp/', $addit_header = [])
	{
		$url = $this->mm->replacer($url, array('https://' => 'http://'));
		$timeout = 10;
		if (strpos($tempDir,'^') !== 0){
			$tempDir = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/'.trim($tempDir,'/').'/';
		} else {
			$tempDir = rtrim(ltrim($tempDir,'^'),'/').'/';
		}

		$name = rtrim($tempDir,'/').'/'.md5($url).time();
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $url);
		//curl_setopt ($ch, CURLOPT_FILE, $fp);
		curl_setopt ($ch, CURLOPT_REFERER, $url);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, 1);
		//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		//curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		curl_setopt($ch, CURLOPT_HEADER,1);
		$userAgent = 'Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.11';
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        if ($addit_header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $addit_header);
        }
		if (stripos($url,'https') != false) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		}
		$data = $this->curlRedirExec($ch);
		if (!$data) $this->mm->log('Error Download file by url['.$url.']:'.$this->getErrorMessage());
		//echo $data;
		curl_close ($ch);
		$fp = fopen($name, "w+");
		fwrite($fp,$data);
		fclose ($fp);
		chmod($name, $this->created_file_mode);
		//unlink($name);
		return ''.$name;
	}
	/** insertFile
	 * Копирует файл в необходимое место и имя
	 * @param type $temp_name Временный файл
	 * @param type $path_main Директория куда копировать Остновная
	 * @param type $path_add Добавочная дирректория возвращается вместе с имененем файла для вставки в БД
	 * @param type $entitle Название файла
	 * <br/> {#rand} - 32 md5   символа рандом
	 * <br/> {#rand6} - 6 рандомных символа из md5
	 * <br/> {#time} - timestamp
	 * @param type $extension Расширение файла (необязательный)
	 * @return string
	 */
	public function insertFile($temp_name, 
		$path_main, $path_add,
		$entitle, $extension = '')
	{
		$path_add = trim($path_add,'/');
		$direct_temp = '';
		if (strpos($path_main,'^')!==0){
			$path_main = trim($path_main,'/');
			$direct_temp = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/'.trim($path_main,'/').'/';
		} else {
			$direct_temp = rtrim(ltrim($path_main,'^'),'/').'/';
		}


		//  create directories
		if ($path_add !== '') $direct_temp .= $path_add.'/';
		if (!file_exists($direct_temp)){
			mkdir($direct_temp, $this->created_file_mode, true);
		}
		if (strpos($entitle,'{#rand}') !== false){
			$entitle = str_replace('{#rand}',md5(filesize($temp_name).rand(0, 10000)),$entitle);
		}
		if (strpos($entitle,'{#rand6}') !== false){
			$entitle = str_replace('{#rand6}',substr(md5(filesize($temp_name).rand(0, 10000)),0,6),$entitle);
		}
		if (strpos($entitle,'{#time}') !== false){
			$entitle = str_replace('{#time}',time(),$entitle);
		} else {
			$entitle = $entitle;
		}
		if ($extension){
			$new_name = $entitle.".$extension";
		} else {
			$new_name = ''.$entitle;
		}
		if (!copy($temp_name, $direct_temp.$new_name)) return '';
		chmod($direct_temp.$new_name, $this->created_file_mode);
		if ($path_add) $path_add .= '/';
		return $path_add.$new_name;
	}
	/** insertImage
	 * Копирует изображение и преобразовывает его ресайзит, обрезает, помещает в нужную дирректорию
	 * [TODO]  наложение изображений как водный знак
	 * @param type $temp_name Временный файл с изображением
	 * @param type $path_main Директория куда копировать Остновная
	 * @param type $path_add Добавочная дирректория возвращается вместе с имененем файла для вставки в БД
	 * @param type $entitle Название файла
	 * <br/> {#rand} - 32 md5   символа рандом
	 * <br/> {#rand6} - 6 рандомных символа из md5
	 * <br/> {#time} - timestamp
	 * @param type $width выходной размер картинки ширина
	 * @param type $height выходной размер картинки высота
	 * @param type $resize_type Тип ресайза
	 * <br/><b>''</b> - тупо ресайзит не соблюдая пропорции
	 * <br/><b>'w'</b> - Ресайзит по ширине, не принимает во внимание  $height
	 * <br/><b>'h'</b> - Ресайзит по высоте, не принимает во внимание  $width
	 * <br/><b>'max'</b> - Вписывает картинку в контейнер $width x $height (т.е. новые стороны могут быть меньше контейнера)
	 * <br/><b>'cut'</b> - Ресайзит и отрезает лишнее, чтобы картинка стала $width x $height (отрежет края картинки)
	 * @param type $srcx - смещение X на исходной картинке откуда обрезать при CROP
	 * @param type $srcy - смещение Y на исходной картинке откуда обрезать при CROP
	 * @param type $src_width_c - ширина на исходной картинки для обрезания при CROP
	 * @param type $src_height_c - высота на исходной картинки для обрезания при CROP
	 * @param type $output Функция возвращает информацию о обрезании для сохранения, чтобы производить новый CROP
	 * <br/><b>orig_width</b> - Ширина оригинального изображения
	 * <br/><b>orig_height</b> - Высота оригинального изображения
	 * <br/><b>src_width</b> - Ширина откропленного части на исходнике
	 * <br/><b>src_heigh</b> - Высота откропленного части на исходнике
	 * <br/><b>srcx</b> - смещение X на исходной картинке откуда обрезать при CROP
	 * <br/><b>srcy</b> - смещение Y на исходной картинке откуда обрезать при CROP
	 * <br/><b>new_width</b> - Ширина нового изображения
	 * <br/><b>new_height</b> - Высота нового изображения
	 * @param type $opt
	 * <br/><b>out_ext</b> - Выходное расширение [jpg,png,gif]
	 * <br/><b>save_little</b> - Сохранить если входное изображение меньше чем необходимое
	 * <br/><b>quality</b> - для out_ext jpg
	 * @return mixed Стркка остаточный путь к картинке или false  при ошибке
	 */
	public function insertImage($temp_name, 
		$path_main, $path_add,
		$entitle,
		$width = 0, $height = 0, 
		$resize_type = '',
		$srcx = false, $srcy = false, 
		$src_width_c = 0, $src_height_c = 0, 
		&$output = array(), $opt = array()) {
	
		
		$path_add = trim($path_add,'/');
		$direct_temp = '';
		if (strpos($path_main,'^')!==0){
			$path_main = trim($path_main,'/');
			$direct_temp = '/'.$path_main.'/';
			if (strpos($path_main,trim($_SERVER['DOCUMENT_ROOT'],'/')) !== 0){
				$direct_temp = rtrim($_SERVER['DOCUMENT_ROOT'],'/').$direct_temp;
			}
		} else {
			$direct_temp = rtrim(ltrim($path_main,'^'),'/').'/';
		} 

		
		// create directories 
		if ($path_add !== '') $direct_temp .= $path_add.'/';
		if (!file_exists($direct_temp)){
			mkdir($direct_temp, $this->created_file_mode, true);
			chmod($direct_temp, $this->created_file_mode);
		}
		// name
		$size = @getimagesize($temp_name);
		if(!$size)  return $this->setErrorMessage('invalid file type');

		$type = $size['mime'];
		$new_name = 'none';
		if (strpos($entitle,'{#rand}') !== false){
			$entitle = str_replace('{#rand}',md5(filesize($temp_name).$size[0].$size[1].rand(0, 10000)),$entitle);
		}
		if (strpos($entitle,'{#rand6}') !== false){
			$entitle = str_replace('{#rand6}',substr(md5(filesize($temp_name).rand(0, 10000)),0,6),$entitle);
		}
		if (strpos($entitle,'{#time}') !== false){
			$entitle = str_replace('{#time}',time(),$entitle);
		} else {
			$entitle = $entitle;
		}
		switch ($type) {
			case 'image/jpeg':
			$new_name = (isset($opt['out_ext']))?$entitle.'.'.$opt['out_ext']:$entitle.'.jpg';
			break;
			case 'image/png':
			$new_name = (isset($opt['out_ext']))?$entitle.'.'.$opt['out_ext']:$entitle.'.png';
			break;
			case 'image/gif':
			$new_name = (isset($opt['out_ext']))?$entitle.'.'.$opt['out_ext']:$entitle.'.gif';
			break;
		}
		if ($new_name === 'none') return $this->setErrorMessage('unsupport MIME type');
		// resize calc
		$new_width = 0;
		$new_height = 0;
		$src_width = $size[0];
		$src_height = $size[1];
		$output['orig_width'] = $src_width;
		$output['orig_height'] = $src_height;
		if (isset($opt['save_little']) && $opt['save_little']
			&& $resize_type === 'max' && $src_width < $width &&  $src_height < $height
		){
			if (isset($opt['out_ext']) && $opt['out_ext'] == 'jpg' && $type == 'image/jpeg' && !isset($opt['nocopy'])){
				$width = 0; $height = 0;
			} else {
				$width = $src_width; $height = $src_height;
			}
		}
		if ($width && $height){
			if ($resize_type == '') { // simple resize
				$new_width = $width;
				$new_height = $height;
			} elseif ($resize_type == 'w') { // resize by width
				$new_width = $width;
				$new_height = $width * $size[1] / $size[0];
			} elseif ($resize_type == 'h') { // resize by height
				$new_height = $height;
				$new_width = $height * $size[0] / $size[1];
			} elseif ($resize_type == 'max') { // resize by max
				if ((1.0 * $width / $height) < (1.0 * $size[0] / $size[1]) ) {
					$new_width = $width;
					$new_height = $width * $size[1] / $size[0];
				} else {
					$new_height = $height;
					$new_width = $height * $size[0] / $size[1];
				}
			} elseif ($resize_type == 'min') { // cut resize
				if ((1.0 * $width / $height) > (1.0 * $size[0] / $size[1]) ) {
					$new_width = $width;
					$new_height = $width * $size[1] / $size[0];
				} else {
					$new_height = $height;
					$new_width = $height * $size[0] / $size[1];
				}
			} elseif ($resize_type == 'cut') {
				if ((1.0 * $width / $height) > (1.0 * $size[0] / $size[1]) ) {
					$src_width = $size[0];
					$src_height = intval($src_width * $height / $width);
					if ($srcy === false) $srcy = $this->sqlInt(($size[1]-$src_height)/2,0);
					$new_width = $width;
					$new_height = $height;
				} else {
					$src_height = $size[1];
					$src_width = intval($src_height * $width / $height);
					if ($srcx === false) $srcx = $this->sqlInt(($size[0]-$src_width)/2,0);
					$new_width = $width;
					$new_height = $height;
				}
			}
		} else {
			if (!copy($temp_name, $direct_temp.$new_name)) return $this->setErrorMessage('fail of copy file');
			chmod($direct_temp.$new_name, $this->created_file_mode);
			if ($path_add !== '') $path_add .= '/';
			return $path_add.$new_name;
		}
		$new_height = intval($new_height);
		$new_width = intval($new_width);
		if ($src_height_c && $src_width_c) {
			$src_width = $src_width_c;
			$src_height = $src_height_c;
		}
		$output['src_width'] = $src_width;
		$output['src_height'] = $src_height;
		if ($srcx === false) $srcx = 0;
		$output['srcx'] = $srcx;
		if ($srcy === false) $srcy = 0;
		$output['srcy'] = $srcy;
		$output['new_width'] = $new_width;
		$output['new_height'] = $new_height;
		// resizing
		if (!$new_height || !$new_width) return $this->setErrorMessage('width or height equal zero');

		$src = false;
		if ($type == 'image/jpeg'){
			$src = @imagecreatefromjpeg($temp_name);
		} elseif($type == 'image/png') {
			$src = @imagecreatefrompng($temp_name);
		} elseif($type == 'image/gif') {
			$src = @imagecreatefromgif($temp_name);
		} else {
			return $this->setErrorMessage('unsupport MIME type when open');
		}
		if (!$src) {
			return $this->setErrorMessage('unsupport file, open error');
		}
		$dest = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($dest, $src, 0,0,$srcx, $srcy, $new_width, $new_height, $src_width, $src_height);
		
		if (isset($opt['tsign_text'])){
			if (!isset($opt['tsign_ttf'])) $opt['tsign_ttf'] = $this->g($this->app_properties['imageInsert_tsign_ttf']);
			if (!isset($opt['tsign_size'])) $opt['tsign_size'] = $this->g($this->app_properties['imageInsert_tsign_size'],5);
			if (!isset($opt['tsign_color'])) $opt['tsign_color'] = $this->g($this->app_properties['imageInsert_tsign_color'],array(255,255,255));
			$tsign_color = imagecolorallocate($dest, $opt['tsign_color'][0], $opt['tsign_color'][1], $opt['tsign_color'][2]);
			if ($opt['tsign_ttf']){
				$tsign_box = imagettfbbox($opt['tsign_size'], 0, $opt['tsign_ttf'], $opt['tsign_text']);
				// bl xy, br xy, tr xy, tl xy
				$tsign_x = $new_width - ($tsign_box[4]-$tsign_box[0]) - $tsign_box[6] - 3;
				$tsign_y = $new_height - ($tsign_box[1]-$tsign_box[5]) - $tsign_box[7] - 3;
				imagettftext($dest, $opt['tsign_size'], 0, $tsign_x, $tsign_y, $tsign_color, $opt['tsign_ttf'], $opt['tsign_text']);
			} 
		}
		if (isset($opt['tsign_png']) && $opt['tsign_png'] && file_exists($opt['tsign_png']) && is_file($opt['tsign_png'])){
			if (!isset($opt['imageInsert_tsign_png_percent'])) $opt['imageInsert_tsign_png_percent'] = $this->g($this->app_properties['imageInsert_tsign_png_percent']);
			if (!isset($opt['imageInsert_tsign_png_x_position'])) $opt['imageInsert_tsign_png_x_position'] = $this->g($this->app_properties['imageInsert_tsign_png_x_position']);
			if (!isset($opt['imageInsert_tsign_png_y_position'])) $opt['imageInsert_tsign_png_y_position'] = $this->g($this->app_properties['imageInsert_tsign_png_y_position']);
			$tsign_png_size = @getimagesize($opt['tsign_png']);
			$tsign_png_src = @imagecreatefrompng($opt['tsign_png']);
			if ($tsign_png_size && $tsign_png_src){
				$src_tsign_box_width = intval($new_width * $opt['imageInsert_tsign_png_percent'] / 100);
				$src_tsign_box_height = intval($new_height * $opt['imageInsert_tsign_png_percent'] / 100);
				$tsign_png_width =  $tsign_png_height = 0;
				if ((1.0 * $src_tsign_box_width / $src_tsign_box_height) < (1.0 * $tsign_png_size[0] / $tsign_png_size[1]) ) {
					$tsign_png_width = $src_tsign_box_width;
					$tsign_png_height = $src_tsign_box_width * $tsign_png_size[1] / $tsign_png_size[0];
				} else {
					$tsign_png_height = $src_tsign_box_height;
					$tsign_png_width = $src_tsign_box_height * $tsign_png_size[0] / $tsign_png_size[1];
				}
				$tsign_png_dest_x = 0; $tsign_png_dest_y = 0;
				if ($opt['imageInsert_tsign_png_x_position'] == 'center') $tsign_png_dest_x = intval(($new_width - $tsign_png_width ) / 2);
				if ($opt['imageInsert_tsign_png_x_position'] == 'right') $tsign_png_dest_x = $new_width - $tsign_png_width;
				if ($opt['imageInsert_tsign_png_y_position'] == 'center') $tsign_png_dest_y = intval(($new_height - $tsign_png_height ) / 2);
				if ($opt['imageInsert_tsign_png_y_position'] == 'bottom') $tsign_png_dest_y = $new_height - $tsign_png_height;
				imagecopyresampled($dest, $tsign_png_src, $tsign_png_dest_x, $tsign_png_dest_y, 0, 0, $tsign_png_width, $tsign_png_height, $tsign_png_size[0], $tsign_png_size[1]);
			}
		}
		$output_type = $type;
		if (isset($opt['out_ext'])){
			if ($opt['out_ext'] == 'jpg') $output_type = 'image/jpeg';
			if ($opt['out_ext'] == 'png') $output_type = 'image/png';
			if ($opt['out_ext'] == 'gif') $output_type = 'image/gif';
		}
		if ($output_type == 'image/jpeg'){
			$quality = (isset($opt['quality']))?intval($opt['quality']):85;
			imagejpeg($dest, $direct_temp.$new_name, $quality);
		} elseif($output_type == 'image/png') {
			imagepng($dest, $direct_temp.$new_name);
		} elseif($output_type == 'image/gif') {
			imagegif($dest, $direct_temp.$new_name);
		} else {
			return $this->setErrorMessage('unsupport MIME type when save');
		}
		imagedestroy($src);
		imagedestroy($dest);
		chmod($direct_temp.$new_name, $this->created_file_mode);
		if ($path_add !== '') $path_add .= '/';
		return $path_add.$new_name;  
	}
	/** exifReader
	 * Получение Exif  информации из фотографии
	 * @param type $file
	 * @return type
	 * longitude,  latitude, make, model, exposure, aperture, apertureValue,
	 * iso, focalLength35mm, focalLength, meteringMode, flash, exposureBiasValue,
	 * sensingMethod, gainControl, exposureProgram, maxApertureValue, datetime,
	 * orientation
	 */
	public function exifReader($file){
		$size = @getimagesize($file);
		if (!$size) return array();
		$type = $size['mime'];
		if ($type !== 'image/jpeg') return array();
		if (!function_exists('exif_read_data')) return array();
		$exif_data = @exif_read_data($file,'GPS,IFD0,EXIF',0);
		if (!is_array($exif_data)) return array();
		$ret = array();
		if (isset($exif_data['MakerNote'])) unset($exif_data['MakerNote']);
		foreach($exif_data as $key => $val){
			if (strpos($key,'UndefinedTag') !== false){
				unset($exif_data[$key]);
			}
		}
		//echo '<!-- '; $this->debugParam($exif_data); echo ' -->';
		if (isset($exif_data["GPSLongitude"])){
			$ret['longitude'] = $this->exifrGetGps(
				$this->g($exif_data["GPSLongitude"],array()), 
				$this->g($exif_data['GPSLongitudeRef']));
		} else {
			$ret['longitude'] = '';
		}
		if (isset($exif_data["GPSLatitude"])){
			$ret['latitude'] = $this->exifrGetGps(
				$this->g($exif_data["GPSLatitude"],array()), 
				$this->g($exif_data['GPSLatitudeRef']));
		} else {
			$ret['latitude'] = '';
		}
		$ret['make'] = $this->g($exif_data['Make']);
		$ret['model'] = $this->g($exif_data['Model']);
		$ret['exposure'] = $this->exifr2Num($this->g($exif_data['ExposureTime']),'1/x');
		$ret['aperture'] = $this->g($exif_data['COMPUTED']['ApertureFNumber']);
		if (!$ret['aperture']) {
			$fNum = $this->g($exif_data['FNumber']);
			if ($fNum) $ret['aperture'] = 'f/'. number_format(floatval($this->exifr2Num($fNum)),1);
		}
		if (isset($exif_data['ApertureValue'])){
			$ret['apertureValue'] = number_format(floatval($this->exifr2Num($this->g($exif_data['ApertureValue']))),3);
		} else {
			$ret['apertureValue'] = '';
		}
		$ret['iso'] = $this->g($exif_data['ISOSpeedRatings']);
		$ret['focalLength35mm'] = $this->exifr2Num($this->g($exif_data['FocalLengthIn35mmFilm']));
		$ret['focalLength'] = $this->exifr2Num($this->g($exif_data['FocalLength']));
		$ret['meteringMode'] = $this->g($exif_data['MeteringMode']);
		$ret['flash'] = $this->g($exif_data['Flash']);
		$ret['exposureBiasValue'] = $this->g($exif_data['ExposureBiasValue']);
		$ret['sensingMethod'] = $this->g($exif_data['SensingMethod']);
		$ret['gainControl'] = $this->g($exif_data['GainControl']);
		$ret['exposureProgram'] = $this->g($exif_data['ExposureProgram']);
		if (isset($exif_data['ApertureValue'])){
			$ret['maxApertureValue'] = number_format(floatval($this->exifr2Num($this->g($exif_data['MaxApertureValue']))),3);
		} else {
			$ret['maxApertureValue'] = '';
		}
		$ret['datetime'] = $this->g($exif_data['DateTime']);
		if (!$ret['datetime']) $ret['datetime'] = $this->g($exif_data['DateTimeOriginal']);
		if (!$ret['datetime']) $ret['datetime'] = $this->g($exif_data['DateTimeDigitized']);
		if ($ret['datetime']) $ret['datetime'] = $this->exifrFormatDate($ret['datetime']);
		$ret['orientation'] = $this->g($exif_data['Orientation']);
		return $ret;
	}
	private function exifrFormatDate($date){
		$tdate = $date;
		if (mb_strlen($date)>10){
			$date1 = substr($date, 0, 10);
			$date2 = substr($date, 10);
			$date1 = str_replace(':', '-', $date1);
			$tdate = $date1.$date2;
		}
		return $this->sqlString($tdate,255,'datetime');
	}
	private function exifrGetGps($exifCoord, $hemi) {
		$degrees = count($exifCoord) > 0 ? $this->exifr2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? $this->exifr2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? $this->exifr2Num($exifCoord[2]) : 0;
		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
		return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	}
	private function exifr2Num($coordPart,$type='') {
		$parts = explode('/', $coordPart);
		if (count($parts) <= 0) return '';
		if (count($parts) == 1) return $parts[0];
		if ($type == '1/x'){
			return '1/'.intval($parts[1] / floatval($parts[0]));
		}
		return floatval($parts[0]) / floatval($parts[1]);
	}
	/** $lastResponseInfo
	 * Информация о заголовках последнего запрсоа curlRequest
	 * header, cookie
	 * @var type 
	 */
	public $lastResponseInfo = array('header'=>'','cookie'=>array());
	/** curlRequest
	 * Выполняет запрос к URL через curl.
	 * Позволяет сохранять cookie между запросами.
	 * Заголовок и куки сохраняются в $this->lastResponseInfo.
	 * @param type $url URL
	 * @param type $type Тип запроса ['GET','POST','GET_HTTPS', 'POST_HTTPS']
	 * @param type $post_data Для POST array(key => value)
	 * @param type $addit_header Дополнительные заголовки array('Accept:application/json');
	 * @param type $options
	 * <br/><b>saveheader</b> - true - Сохраняет полностью заголовоки
	 * <br/><b>cookie</b> - будут установлены для запроса (обычно берётся из $this->lastResponseInfo['cookie'])
	 * @return boolean Тело ответа
	 */
	public function curlRequest($url, $type = 'GET', $post_data = '', $addit_header = array(), $options = array())
	{
		$saveheader = (isset($options['saveheader']))?true:false;
		$cookie = '';
		if (isset($options['cookie']) && is_array($options['cookie'])){
			foreach ($options['cookie'] as $key => $value) {
				$cookie .= $key.'='.$value.'; ';
			}
		}
		$timeout = 10;
		$responseCURL = '';
		$method = 'GET';
		if (($type == 'POST') || ($type == 'POST_HTTPS')) $method = 'POST';
		if ($type == 'HEAD') { $method = 'HEAD'; $saveheader = true; }
		$secure = false;
		if (($type == 'GET_HTTPS') || ($type == 'POST_HTTPS')) $secure = true;
		try {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			if ($saveheader) {
				curl_setopt($ch, CURLOPT_HEADER,true); 
			} else {
				curl_setopt($ch, CURLOPT_HEADER,0); 
			}
			if ($cookie){
				curl_setopt($ch, CURLOPT_COOKIE,$cookie);
			}
			if ($this->g($options['referer'])){
				curl_setopt($ch, CURLOPT_REFERER, $this->g($options['referer']) );
			}
			if (isset($options['encoding']) && $options['encoding'] == 'gzip'){
				curl_setopt($ch, CURLOPT_ENCODING, "gzip");
			}
			if ($method == 'POST') {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
				if (is_array($post_data)){
					$post_data = http_build_query($post_data);
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($ch, CURLOPT_POST, 1);
			}
			if ($method == 'HEAD') {
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'HEAD');
				curl_setopt($ch, CURLOPT_NOBODY, true);
			}

			if ($secure) {
				if ($this->g($options['ssl'])){
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
					curl_setopt($ch, CURLOPT_CAINFO, $options['ssl']);
				} else {
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				}
			}
			

			$useragent = $this->g($options['useragent'],'Opera/9.80 (Windows NT 6.1) Presto/2.12.388 Version/12.17');
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			if ($this->g($options['no_redirects'])){
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			} else {
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			}
			
			
			/*
				curl_setopt($ch, CURLOPT_COOKIE,$coo);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_REFERER, $refer );
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ch, CURLOPT_USERAGENT, В«Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)В»);
				curl_setopt($ch, CURLOPT_VERBOSE,1); 
				return $data = curl_exec($ch);
				$type=curl_multi_getcontent($ch)
			 */
			if ($addit_header) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $addit_header);
			}
			if ($this->g($options['debug'])){
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				$verbose = fopen('php://temp', 'w+');
				curl_setopt($ch, CURLOPT_STDERR, $verbose);
				
			}
			$responseCURL = curl_exec($ch);
			$this->lastResponseInfo['info'] = curl_getinfo($ch);
			if ($this->g($options['debug'])){
				rewind($verbose);
				$verboseLog = stream_get_contents($verbose);
				$this->lastResponseInfo['debug'] = $verboseLog;
			}
			if ($saveheader){
				$header = substr($responseCURL,0,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
				$responseCURL = substr($responseCURL,curl_getinfo($ch,CURLINFO_HEADER_SIZE));
				preg_match_all("/Set-Cookie: (.*?)=(.*?);/i",$header,$res);
				foreach ($this->g($res[1],array()) as $key => $value) {
					$this->lastResponseInfo['cookie'][$value] = $res[2][$key];
				}
				$this->lastResponseInfo['header'] = $header;
			}
			curl_close($ch);
		} catch (Error $ex) {
			echo 'Curl Connection Error';
			$this->debugParam($ex);
			return false;
		}
		return $responseCURL;
	}
	/** requestUri
	 * Парсинг и другие манипуляции с GET  параметрами
	 * $obj = requestUri() - возвращает распаршенный объект  из REQUEST_URI
	 * $uri_string = requestUri($obj) - восстанавливает строку REQUEST_URI из объекта
	 * $obj = requestUri(false,$obj) -  клонирует объект для изменений.
	 * @param type $from
	 * @param type $coper
	 * @return string
	 */
	public function requestUri($from = false, $coper = false){
		if ($from === false){ //decode and return requestUri object
			$ret = array('path'=>'','params'=>array());
			if ($coper === false){
				$spliter = explode('?',$_SERVER['REQUEST_URI']);
				$ret['path'] = $spliter[0];
				if (count($spliter) > 1){
					$params = explode('&',$spliter[1]);
					foreach($params as $par){
						$parKV = explode('=',$par);
						if ($parKV[0] == 'body_ajax') continue;
						$ret['params'][$parKV[0]] = (isset($parKV[1]))?urldecode($parKV[1]):'';
					}
				}
			} else {
				$ret['path'] = $coper['path'];
				foreach($coper['params'] as $parK => $parV){
					$ret['params'][$parK] = $parV;
				}
			}
			return $ret;
		} else { // encode requestUri Object to Request String 
			$uri = $from['path'];
			if (count($from['params'])>0){
				$uri_params = '';
				foreach($from['params'] as $parK => $parV){
					if ($uri_params) $uri_params .= '&';
					else $uri_params .= '?';
					$uri_params .= $parK.'='.urlencode($parV);
				}
				$uri .= $uri_params;
			}
			return $uri;
		}
	}
	
	public function colorConvert($color, $opacity = 1){
		if (is_string($color)){
			if ($color[0] == '#') $color = substr($color, 1 );
			if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
				return 'rgb(0,0,0)';
			}
			$rgb = array_map('hexdec', $hex);
			if ($opacity === false) {
				return 'rgb('.implode(",",$rgb).')';
			} else {
				return 'rgba('.implode(",",$rgb).','.$opacity.')';
			}
		} else if (is_array($color)) {
			$rgb = array_map('dechex', $color);
			return '#'.$rgb[0].$rgb[1].$rgb[2];
		} else {
			return '[CANT CONVERT COLOR]';
		}
	}


	// ********************************
	// **** Дополнительные функции ****
	// ********************************
	/**
	 * Генерирование случайной строки
	 * @param int $length Длина
	 * @param string $characters Допустимые символы
	 * @return string
	 */
	public function generateRandomString($length = 32, $characters = '') {
		if (!$characters) $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	/** Alpha - нужен тест функции
	 * Поворот изображения
	 * @param type $img
	 * @param type $rotation
	 * @return boolean
	 */
	public function rotateImage($img, $rotation) {
		$width = imagesx($img);
		$height = imagesy($img);
		switch($rotation) {
		 case 90: $newimg= @imagecreatetruecolor($height , $width );break;
		 case 180: $newimg= @imagecreatetruecolor($width , $height );break;
		 case 270: $newimg= @imagecreatetruecolor($height , $width );break;
		 case 0: return $img;break;
		 case 360: return $img;break;
		}
		if($newimg) { 
		 for($i = 0;$i < $width ; $i++) { 
		   for($j = 0;$j < $height ; $j++) {
			 $reference = imagecolorat($img,$i,$j);
			 switch($rotation) {
				case 90: if(!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference )){return false;}break;
				case 180: if(!@imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference )){return false;}break;
				case 270: if(!@imagesetpixel($newimg, $j, $width - $i, $reference )){return false;}break;
			 }
		   } 
		 } return $newimg; 
		} 
		return false;
	}
	/**
	 * str_pad для строк UTF-8 или другой кодировки
	 * @param type $input
	 * @param type $pad_length
	 * @param type $pad_string
	 * @param type $pad_style
	 * @param type $encoding
	 * @return type
	 */
	public function mbStrPad ($input, $pad_length, $pad_string, $pad_style, $encoding="UTF-8"){
		return str_pad($input,strlen($input)-mb_strlen($input,$encoding)+$pad_length, $pad_string, $pad_style);
	}
	/**
	 * Первая буква заглавная c сохранением кодировки
	 * @param string $string
	 * @return string
	 */
	public function mbUcfirst($string) { 
       $string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
       return $string;
    }
	/**
	 * Получение информации о Youtube видео по коду видео или по url
	 * @param type $opt
	 * @return array
	 * <br/> id
	 * <br/> author = array(name => ,gdata_uri => )
	 * <br/> title
	 * <br/> description
	 * <br/> keywords
	 * <br/> url
	 * <br/> duration
	 * <br/> picture
	 * <br/> thumbnails array[] of array(url=>,time=>,width=>,height=>)
	 * 
	 */
	public function youtubeData($opt = array()){
		$videocode = '';
		if (isset($opt['videocode'])) $videocode = $opt['videocode'];
		if (!$videocode && isset($opt['url'])){
			$matches = array();
			if (preg_match('/[.]*v=([^&]+)[.]*/i', $opt['url'], $matches)) {
			} else {
				if (preg_match('/[.]*youtu\.be\/([^&]+)[.]*/i', $opt['url'], $matches)) {
				}else {
					if (preg_match('/[.]*youtube\.com\/embed\/([^&]+)[.]*/i', $opt['url'], $matches)){}
				}
			}
			if (!$matches) return $this->setErrorMessage('Неверная ссылка');
			$videocode = $this->g($matches[1]);
		}
		if (!$videocode) return $this->setErrorMessage('Не опознат ключ видео');
		//$response_json = $this->curlRequest('http://gdata.youtube.com/feeds/api/videos/'.$videocode.'?alt=json');
		$response_json = $this->curlRequest('https://www.googleapis.com/youtube/v3/videos?id='.$videocode.'&key='.$this->app_properties['google_api_key'].'&part=snippet,contentDetails','GET_HTTPS');
		if ($response_json === 'Invalid id') return $this->setErrorMessage('Неверный ID');
		$video_info = json_decode($response_json, true);
		if ($this->g($video_info['error'])) return $this->setErrorMessage('JSON Error');
		
		$ret_info = array();
		$ret_info['id'] = $this->g($video_info['items'][0]['id']);
		$ret_info['type'] = 'youtube';
		$ret_info['author'] = array(
			'name' => $this->g($video_info['items'][0]['snippet']['channelTitle']),
			'gdata_uri' => 'https://www.youtube.com/channel/'.$this->g($video_info['items'][0]['snippet']['channelId']),
		);
		$ret_info['title'] = $this->g($video_info['items'][0]['snippet']['title']);
		$ret_info['description'] = $this->g($video_info['items'][0]['snippet']['description']);
		$ret_info['keywords'] =  '';
		$ret_info['url'] = 'http://www.youtube.com/embed/'.$ret_info['id'];
		$ret_info['embed_data'] = $ret_info['id'];
		$ret_info['duration'] = 0;
		try {
			if (!class_exists('DateTime') || !class_exists('DateInterval')){ // PHP 5.2
				$ret_info['duration'] = $this->youtube_time_to_seconds($this->g($video_info['items'][0]['contentDetails']['duration']));
			} else {
				$start = new DateTime('@0'); // Unix epoch
				$start->add(new DateInterval($this->g($video_info['items'][0]['contentDetails']['duration'])));
				$ret_info['duration'] = $start->getTimestamp();
			}
		} catch(Exception $ex) {}
		$ret_info['picture'] = $this->g($video_info['items'][0]['snippet']['thumbnails']['standard']['url']);
		if (!$ret_info['picture']){
			$ret_info['picture'] = $this->g($video_info['items'][0]['snippet']['thumbnails']['high']['url']);
		}
		if (!$ret_info['picture']){
			$ret_info['picture'] = $this->g($video_info['items'][0]['snippet']['thumbnails']['medium']['url']);
		}
		if (!$ret_info['picture']){
			$ret_info['picture'] = $this->g($video_info['items'][0]['snippet']['thumbnails']['default']['url']);
		}
		$ret_info['thumbnails'] = array();
		if (!$ret_info['id']) return $this->setErrorMessage('Ошибка при получении информации о видео');
//		$this->mm->debugParam($video_info);
//		$this->mm->debugParam($ret_info);
		return $ret_info;
	}
	/**
	 * Получение информации о VK видео по коду видео или по url
	 * @param type $opt
	 * @return array
	 * <br/> id
	 * <br/> author = array(name => ,gdata_uri => )
	 * <br/> title
	 * <br/> description
	 * <br/> keywords
	 * <br/> url
	 * <br/> duration
	 * <br/> picture
	 * <br/> thumbnails array[] of array(url=>,time=>,width=>,height=>)
	 * 
	 */
	public function vkVideoData($opt = array()){
		$videocode = '';
		if (isset($opt['videocode'])) $videocode = $opt['videocode'];
		if (!$videocode && isset($opt['url'])){
			$matches = array();
			if (preg_match('/[.]*v=([^&]+)[.]*/i', $opt['url'], $matches)) {
			} else {
				if (preg_match('/[.]*vk\.com\/video([^&\?]+)[.]*/i', $opt['url'], $matches)) {}	
			}
			if (!$matches) return $this->setErrorMessage('Неверная ссылка');
			$videocode = $this->g($matches[1]);
		}
		if (!$videocode) return $this->setErrorMessage('Не опознат ключ видео');
		
		$this->load->model('vk_mauth');
		$row = $this->mauth->getUserRow($this->app_properties['vk_access_user_id']);
		$response = $this->vk_mauth->api($row,'video.get', array('videos'=>$videocode, 'v'=>'5.23'));
		if (!$response) return $this->setErrorMessage('vk_mauth:'.$this->vk_mauth->getErrorMessage());
		$video_info = $this->g($response['response']['items'][0]);
		if (!$video_info) return $this->setErrorMessage('invalid vk data');
		if (!$this->g($video_info['player'])) return $this->setErrorMessage('Ошибка при получении кода плеера');
		if (strpos($video_info['player'],'youtube') !== false){
			echo '['.$video_info['player'].']';
			return $this->youtubeData(array('url' => $video_info['player']));
		}
		//$this->mm->debugParam($video_info);
		
		$ret_info = array();
		$ret_info['id'] = $video_info['owner_id'].'_'.$video_info['id'];
		$ret_info['type'] = 'vk';
		$ret_info['author'] = array('name' => '','gdata_uri' => '',);
		$ret_info['title'] = $this->g($video_info['title']);
		$ret_info['description'] = $this->g($video_info['description']);
		$ret_info['keywords'] = '';
		$ret_info['url'] = $this->g($video_info['player']);
		$ret_info['embed_data'] = $this->g($video_info['player']);
		$ret_info['duration'] = $this->g($video_info['duration']);
		$ret_info['picture'] = $this->g($video_info['photo_320']);
		$ret_info['thumbnails'] = array();
		if (!$ret_info['id']) return $this->setErrorMessage('Ошибка при получении информации о видео');
		return $ret_info;
	}
	/**
	 * Получение информации о VK видео по коду видео или по url
	 * @param type $opt
	 * @return array
	 * <br/> id
	 * <br/> author = array(name => ,gdata_uri => )
	 * <br/> title
	 * <br/> description
	 * <br/> keywords
	 * <br/> url
	 * <br/> duration
	 * <br/> picture
	 * <br/> thumbnails array[] of array(url=>,time=>,width=>,height=>)
	 * 
	 */
	public function vimeoVideoData($opt = array()){
		$videocode = '';
		if (isset($opt['videocode'])) $videocode = $opt['videocode'];
		if (!$videocode && isset($opt['url'])){
			$matches = array();
			if (preg_match('/[.]*vimeo\.com\/video\/([^&\?a-z"]+)[.]*/i', $opt['url'], $matches)) {
			} else {
				if (preg_match('/[.]*vimeo\.com\/([^&\?a-z"]+)[.]*/i', $opt['url'], $matches)) {}	
			}
			if (!$matches) return $this->setErrorMessage('Неверная ссылка');
			$videocode = $this->g($matches[1]);
		}
		if (!$videocode) return $this->setErrorMessage('Не опознат ключ видео');
		
		$vimeo_url = 'http://vimeo.com/'.$videocode;
		$response_json = $this->curlRequest('http://vimeo.com/api/oembed.json?url='.urlencode($vimeo_url).'');
		$video_info = @json_decode($response_json, true);
		if (!$video_info) return $this->setErrorMessage('invalid vimeo data');
		if ($this->g($video_info['type']) !== 'video') return $this->setErrorMessage('notvideo vimeo data');
		
		//$this->mm->debugParam($video_info);		
		//return false
		
		$ret_info = array();
		$ret_info['id'] = $this->g($video_info['video_id']);
		$ret_info['type'] = 'vimeo';
		$ret_info['author'] = array('name' => $this->mm->g($video_info['author_name']),'gdata_uri' => $this->mm->g($video_info['author_url']),);
		$ret_info['title'] = $this->g($video_info['title']);
		$ret_info['description'] = $this->g($video_info['description']);
		$ret_info['keywords'] = '';
		$ret_info['url'] = 'http://player.vimeo.com/video/'.$this->g($video_info['video_id']);
		$ret_info['embed_data'] = $this->g($video_info['video_id']);
		$ret_info['duration'] = $this->g($video_info['duration']);
		$ret_info['picture'] = $this->g($video_info['thumbnail_url']);
		$ret_info['thumbnails'] = array();
		if (!$ret_info['id']) return $this->setErrorMessage('Ошибка при получении информации о видео');
		return $ret_info;
	}

	public $month2 = array(
		'01' => 'Января',
		'02' => 'Февраля',
		'03' => 'Марта',
		'04' => 'Апреля',
		'05' => 'Мая',
		'06' => 'Июня',
		'07' => 'Июля',
		'08' => 'Августа',
		'09' => 'Сентября',
		'10' => 'Октября',
		'11' => 'Ноября',
		'12' => 'Декабря',
	);
	/**
	 * Русский формат даты 9 января 1999
	 * @param type $date
	 * @return type
	 */
	public function rusDate($date){
		return
			$this->mm->date('d',array('time' => $date,'timezone_name' => 'UTC'))
			. ' '
			. $this->month2[$this->mm->date('m',array('time' => $date,'timezone_name' => 'UTC'))]
			. ' '
			. $this->mm->date('Y',array('time' => $date,'timezone_name' => 'UTC'))
		;
	}
	
	
	/** TODO
	 * Конвертация валют (Google калькулятора больше не работает перевести на rate-exchange.appspot.com)
	 * @param type $amount
	 * @param type $from
	 * @param type $to
	 * @return type
	 */
	public function moneyExchangeConvert($amount, $from, $to){
		$ch = curl_init();
		$url = "http://www.google.com/ig/calculator?q={$amount}{$from}=?{$to}";
		//http://rate-exchange.appspot.com/currency?from=BYR&to=USD&q=100000
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);

		$data = array();
		$items = explode(',', str_replace(array('{', '}'), '', $result));
		foreach ($items as $item) {
			$item = str_replace('"', '', $item);
			list($k, $v) = explode(':', $item);
			$data[ $k ] = $v;
		}

		return (float) preg_replace('/[^0-9.]/', '', $data['rhs']);

		//$er = currency_convert(1, 'USD', 'UAH'); // 8.17400829
		//$er = currency_convert(555, 'USD', 'RUB'); // 17644.8146
		//$er = currency_convert(750, 'USD', 'GBP'); // 463.707184
		//$er = currency_convert(2500, 'USD', 'UAH'); // 20435.0207
		//$er = currency_convert(7000, 'USD', 'CNY'); // 42587.8831

		//echo round($er, 3);
	}
	public function textForm($text, $numForm){
		$urlXml = "http://export.yandex.ru/inflect.xml?name=".urlencode($text);
		$result = @simplexml_load_file($urlXml);
		if ($result){
			$arrData = array();
			foreach ($result->inflection as $one) {
			   $arrData[] = (string) $one;
			}
			return $arrData[$numForm];
		}
		return false;
	}
	
	
	/** TODO
	 * Получение псевдо-случайного аватара 
	 */
	public function gravatar($string,$size = 40){
		$default = "http://domain.org/pub/files/images/profile/0/0/m7-20130728165711-preview-a11dd604b5fdb1652f223d5119af33aa.jpg";
		$grav_url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($string))) . 
				"?d=" . urlencode($default ) . "&r=PG&d=identicon&s=" . $size;
		return $grav_url;
	}
	/** TODO
	 * Получение ТИЦ сайта
	 * @return type
	 */
	public function yandexTIC(){
		$url = 'http://habrahabr.ru/';
		$str = file_get_contents("http://bar-navig.yandex.ru/u?ver=2&show=32&url=".$url);
		$obj = simplexml_load_string($str);
		return (int) $obj->tcy['value'];
	}
	/** TODO
	 * Получение снипета сайта
	 * @return string
	 */
	public function snupshot(){
		return 'http://mini.s-shot.ru/1280x800/1280/png/?http://artox-media.by';
	}
	/**
	 * Информация о ГЕО по IP 
	 * @param type $ip
	 * @return type
	 */
	public function getGeoinfoByIp($ip){
		$xml_response = $this->curlRequest('http://api.hostip.info/?ip='.$ip);
		$ret = array();
		preg_match("@<Hostip>(\s)*<gml:name>(.*?)</gml:name>@si", $xml_response, $city_preg);
		$ret['city'] = @$city_preg[2]; 
		preg_match("@<countryName>(.*?)</countryName>@si", $xml_response, $country_preg);
		$ret['country'] = @$country_preg[1];
		preg_match("@<countryAbbrev>(.*?)</countryAbbrev>@si", $xml_response, $countryAbbrev);
		$ret['countryAbbrev'] = @$countryAbbrev[1];
		return $ret;
	}
	
	/**
	 * Рекурсивный CHMOD
	 * @param type $path
	 * @param type $filemode
	 * @param type $dirmode
	 * @return type
	 */
	function dirRecChmod($path, $filemode, $dirmode) { 
		if (is_dir($path) ) { 
			if (!chmod($path, $dirmode)) { 
				$dirmode_str=decoct($dirmode); 
				print "Failed applying filemode '$dirmode_str' on directory '$path'\n"; 
				print "  `-> the directory '$path' will be skipped from recursive chmod\n"; 
				//return; 
			} 
			$dh = opendir($path); 
			while (($file = readdir($dh)) !== false) { 
				if($file != '.' && $file != '..') {  // skip self and parent pointing directories 
					$fullpath = $path.'/'.$file; 
					$this->dirRecChmod($fullpath, $filemode,$dirmode); 
				} 
			} 
			closedir($dh); 
		} else { 
			if (is_link($path)) { 
				print "link '$path' is skipped\n"; 
				return; 
			} 
			if (!chmod($path, $filemode)) { 
				$filemode_str=decoct($filemode); 
				print "Failed applying filemode '$filemode_str' on file '$path'\n"; 
				return; 
			} 
		} 
	}
	

	
	private function youtube_time_to_seconds( $yttime ) {
		$format = false;
		$hours  = $minutes = $seconds = 0;

		$pattern['hms'] = '/([0-9]+)h([0-9]+)m([0-9]+)s/i'; // hours, minutes, seconds
		$pattern['ms']  =          '/([0-9]+)m([0-9]+)s/i'; // minutes, seconds
		$pattern['h']   = '/([0-9]+)h/i';
		$pattern['m']   = '/([0-9]+)m/i';
		$pattern['s']   = '/([0-9]+)s/i';

		foreach ( $pattern as $k => $v ) {

			preg_match( $v, $yttime, $result );

			if ( ! empty( $result ) ) {
				$format = $k;
				break;
			}
		}
		switch ( $format ) {
			case 'hms':
				$hours   = $result[1];
				$minutes = $result[2];
				$seconds = $result[3];
				break;
			case 'ms':
				$minutes = $result[1];
				$seconds = $result[2];
				break;
			case 'h':
				$hours = $result[1];
				break;
			case 'm':
				$minutes = $result[1];
				break;
			case 's':
				$seconds = $result[1];
				break;
			default:
				return false;
		}

		return ( $hours * 60 * 60 ) + ( $minutes * 60 ) + $seconds;
	}
	
	/**
	* Склонение слов по падежам. С использованием api Яндекса
	* @var string $text - текст 
	* @var integer $numForm - нужный падеж. Число от 0 до 5
	*
	* @return - вернет false при неудаче. При успехе вернет нужную форму слова
	*/
	function getNewFormText($text, $numForm){
		$urlXml = "http://export.yandex.ru/inflect.xml?name=".urlencode($text);
		$result = @simplexml_load_file($urlXml);
		if($result){
			$arrData = array();
			foreach ($result->inflection as $one) {
			   $arrData[] = (string) $one;
			}
			return $arrData[$numForm];
		}
		return false;
	}
	
	
}

/*
Version History

v2.6.0.110
 * sql time-limit auto debugger
 * logger in other file
 * console logger
 * colorConvert
 * css php compile compressor
 * getNewFormText

v2.5.0.105
 * Compressor
 * Only some fields add from app_properties to data

v2.3.5.101
 * Youtube fixes, old php 5.2 support

v2.3.4.100
 insertImage: option tsign_png

v2.3.2.98
 mDate Fix, youtube vidoe data fix, sqlString HTML addit

v2.3.1.97
 rollback $_SERVER['REQUEST_URI'] after SUBDOMAIN change

v2.3.0.96
 curlGrabFile (https fix)
 youtubeVideo support api v3

v2.3.0.95 
 mjsaValidator fix 'phone'
 insertImage add minimal text on image option

v2.2.0.94
support getUrl 1.1

v2.2.0.93
add: dbUpdate
ext: sqlString(type = color)

v2.1.2.92 (2014-11-23)
change db

v2.1.1.91 (2014-09-26)
vimeoVideoData

v2.1.0.90 (2014-08-14)
vkVideoData
hot fixes

v2.0.0.85 (2014-06-07)
Rename most functions
unsupport older projects

v1.5.0.82 (2014-05-31)
All module Refactoring
Rename Fields
Url - new functionality
end_of_the_word -> wordEnd
удалена функция parse_object
удалена функция arrCorrect
mjsaEvent, mjsaPrintEvent
mjsaSuccess, mjsaPrintSuccess
mjsaError, mjsaPrintError


v1.4.10.80 (2014-05-08)
mm_config.php to CI config

v1.4.9.79 (2014-05-07) 
total rename mjs to mjsa

v1.4.8.78 (2014-04-22) Critical update
sqlString: fix len correcter
sqlString ext: length_append param
g: fix null value

v1.4.7.77 (2014-03-26)
benckmarks on db_***

v1.4.6.76 (2014-03-03)
add func: dbSelectField
remove ext:  dbSelect postprocessing

v1.4.5.75 (2014-02-19)
 * youtubeData

v1.4.4.74 (2014-02-17)
 * mjsaInsert
 * translit hot fix
 * dbInsert simple

v1.4.3.73
 * getSiteParam (default value)
 * Custom json_encode
 * readFileVersion

v1.4.2.72
 *  Insert image fix: if cant get size - invalid type
 Rewrite insertFile
 dir_locale
 * Some FIXES

v1.4.1.71
 location => request_type 'mjsa_ajax'

v1.4.0.70
config file in mm_config.php

v1.3.16.63
mjsaError fix
 * time

v1.3.15.62 (2013-10-04)
sqlString hot fix

v1.3.14.61 (2013-09-30)
add func: copyright

v1.3.13.60 (2013-09-24)
test_mode parameter

v1.3.13.59 (2013-09-20)
add func: loadtpl

v1.3.11.58 (2013-09-20)
add social default params
fix: insertImage bag in return path

v1.3.10.56 (2013-09-05)
mjsaError: closePopups
insertImage: quality
add func: exif reader
add func: getSiteParam
add func: setSiteParam

v1.3.9.55 (2013-07-20)
mbStrPad

v1.3.8.54 (2013-07-19)
sqlString (opt like)

v1.3.7.53 (2013-07-02)
insertImage: ext opt opt.out_ext

v1.3.6.52
benchmark html comment 

v1.3.5.51
add func: generateRandomString

v1.3.5.50
insertImage: {#rand} support
insertFile: extention simple copy

v1.3.4.49
add func: requestUri (encode decode REQUEST_URI)

v1.3.3.48
add func: firstTrue(array of mixed)

v1.3.2.47
add func: log - logging to the file

46
insertfile extention default action

v1.3.1.45
simple database use
add func: sdbConnect
edit func: curlRequest Update


v1.3.0.44
deprecated remove
dbSelect deprecated remove
insertImage cut center default [beta]

v1.2.5.43
debugParam: vardump support
db_now
dbFromStore
add_func: mjsaError

v1.2.4.42 
benchmark use in debug db_* funcs

v1.2.3.41
add func: secondsToTimeSimple

v1.2.2.40
ext func: sqlString ('\n to <br/>' parameter)
ext func: add support create preview with jCrop

v1.2.1.38
hot fix: sqlString cut to maxlength

v1.2.0.37
add func: validEmail

v1.2.0.36
sqlInt rewrite (no change)
remove deprecated functions:
	-get_rus_date
	-view
	-gi
	-json_encode_cyr
remove func: masort
add func: translit (not full)
add func: firstData
add func: location

v1.1.6.35
masort

v1.1.5.34 (1.0.16.34)
insert image name pattern
sqlInt intval

v1.1.4.33 (1.0.16.33)
store paths to images
~minifix in grabImage default folder now '/pub/files/temp/'


v1.1.3.32 (1.0.16.32)
[deprecated] view (because CI support cached variables)
app_properties array

v1.1.3.31 (1.0.16.31)
add getLanguage and app_languages

v1.1.3.30 (1.0.16.29)
Database Timezone variables for sql 

v1.1.3.29 (1.0.16.29)
add func: dbInsert

v1.1.2.28
label deprecated function gi (get_if)

1.1.2.27(1.0.15.27)
fix db selectes functions (remove slashes arr replace no stripslashess inner function + postprocessing not supported)
 
1.1.1.26(1.0.14.26)
add function g (like isset)
fix view (to string fix)
 
1.1.0.25(1.0.13.25)
fix sqlString html(xss filter) 
 
1.1.0.24(1.0.13.24)
Removed deprecated addon
  
1.0.12
curlRequest (add standart redirect support)
errorMessage (added error functional for future exceptions)
gi <- get_if (deprecated, speed optimization)

1.0.11 
curlRequert (hot useragent fix)
mkdir (now with chmod 0777 fix)
arrCorrect (fix critical error)

1.0.10 
view (no return fix)
arrCorrect (created)

1.0.9
trim_arr (created)
remove_slashes_arr (error fix)
mDateConvert (error fix)


1.0.8
sqlString (separate datetime and date types )
insert_image[deprecated] -> insertImage (filename need only integrate with grab)
insert_file[deprecated] -> insertFile ()
curlGrabFile (created)
sqlString (fix date string type)
mDateConvert (created)
 /////////////////////////////////////////////////////////////////////////////////////
 */