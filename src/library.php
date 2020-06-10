<?php
class i18n
{

	private $i18n_lang = "en"; // default language
	private $i18n_folder = __DIR__;
	private $i18n_domain = "WWW";
	private $i18n_data = array();
	private $i18n_support_lang = array('ko','en','zh','ja','th','vi','km','uz','my');

	public function __construct($p_lang = '')
	{
		$lang = $this->i18n_lang;
		$brwserlang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		if(trim($brwserlang)) {
			$brwserlang = explode(',', trim($brwserlang))[0];
			$brwserlang = explode(';', $brwserlang)[0];
		} else {
			$brwserlang = '';
		}
		
		if ($brwserlang && trim($brwserlang) != '') {
			$lang = $brwserlang;
		} 
		if (isset($_REQUEST['lang']) && trim($_REQUEST['lang']) != '') {
			$lang = $_REQUEST['lang'];
		} 
		if (isset($_COOKIE['lang']) && trim($_COOKIE['lang']) != '') {
			$lang = $_COOKIE['lang'];
		} 
		if (isset($_SESSION['lang']) && trim($_SESSION['lang']) != '') {
			$lang = $_SESSION['lang'];
		}
		if (trim($p_lang)) {
			$lang = $p_lang;
		}
		// var_dump($brwserlang, $_REQUEST['lang'], $_COOKIE['lang'], $_SESSION['lang'], $lang); exit;

		if ($lang == 'kr') {
			$lang = 'ko';
		}
		if ($lang == 'cn') {
			$lang = 'zh';
		}
		// 언어파일
		$pofile = $this->i18n_folder . "/{$lang}/LC_MESSAGES/" . $this->i18n_domain . ".po";
		if (!file_exists($pofile)) {
			$lang = 'ko';
			$pofile = $this->i18n_folder . "/{$lang}/LC_MESSAGES/" . $this->i18n_domain . ".po";
		}

		$this->i18n_lang = $lang;
		@$_REQUEST['lang'] = $lang;

		$c = $this->get_cache($pofile);
		$c = $c ? json_decode($c) : false;
		if (file_exists($pofile)) {
			if (isset($c->gentime) && $c->gentime >= filemtime($pofile)) { // 캐시 생성 시간이 파일 수정시간보다 크면 캐시 사용. 
				$this->i18n_data = (array) $c->data;
			} else {
				$data = $this->parse_po($pofile);
				$this->set_cache($pofile, json_encode($data), 31536000); // 1년 캐시
				$data = array(
					'gentime' => time(),
					'data' => $data
				);
				$this->i18n_data = $data ? (array) $data['data'] : array();
			}
		} else {
			$this->i18n_data = array();
		}
	}

	public function parse_po($pofile) {
		$data = array();
		// var_dump(file_exists($pofile), $pofile);exit;
		if (!file_exists($pofile)) {
			return $data;
		}
		$con = file_get_contents($pofile);
		// var_dump(preg_replace('/^#(.*)/m', '', $con));exit;
		$con = preg_replace('/^#(.*)/m', '', $con); // remove comment
		$con = preg_replace('/^\"(Project-Id-Version|Report-Msgid-Bugs-To|POT-Creation-Date|PO-Revision-Date|Last-Translator|Language-Team|Language|MIME-Version|Content-Type|Content-Transfer-Encoding|X-Generator|Plural-Forms):(.*)\"$/m', '', $con); // remove header
		$con = str_replace('"' . "\n" . '"', '', $con); // concat multiline string
		$con = explode("\n", $con);
		// var_dump($con); exit;
		$msgid = array();
		$msgstr = array();
		// $test = false;
		foreach ($con as $row) {
			if (trim($row) != '' && (strpos($row, 'msgid') !== false || strpos($row, 'msgstr') !== false)) { // 빈줄 재외, msgid, msgstr 없는것 제외
				preg_match('/^(.*)\s"(.*)"$/', $row, $matches); // 키 "값" 으로 추출
				if (isset($matches[1]) && trim($matches[1]) != '') {
					$key = $matches[1];
					$val = $matches[2] ? $matches[2] : '';
					// if($key=='Comment') { $test = 1;}
					if ($key == 'msgid' || $key == 'msgstr') {
						$$key[] = $val;
					}
					// if($test) { 
					// 	var_dump('key:'. $key.',value:'. $val. '. count(msgid):'. count($msgid) .',  count(msgstr):'. count($msgstr));
					// }
				}
			}
		}
		if (count($msgid) == count($msgstr)) {
			for ($i = 0; $i < count($msgid); $i++) {
				$data[$msgid[$i]] = $msgstr[$i];
			}
		} else {
			var_dump(count($msgid) , count($msgstr)); 
			for($i=0; $i<count($msgid); $i++) {
			var_dump($i, $msgid[$i] , $msgstr[$i]); 
			}
			exit('데이터 키('.count($msgid).')와 값('.count($msgstr).')이 맞지 않습니다. ^msgid.*\nmsgid  검색어로 검색해보세요.  ');
		}
		return $data;
	}

	public function get_i18n_lang()
	{
		return $this->i18n_lang;
	}

	/**
	 * 캐시 내용 가져오기. 
	 * @param String id cache id
	 * @param Number sec cache time(sec.)
	 */
	public function get_cache($id)
	{
		$r = '';
		$_cache_file = $this->get_cache_file_path($id);
		if (file_exists($_cache_file)) {
			$r = unserialize(gzuncompress(file_get_contents($_cache_file)));
			if ($r['time'] > time()) {
				$r = $r['contents'];
			} else {
				$r = '';
			}
		}
		return $r;
	}

	public function set_cache($id, $contents, $sec = 60)
	{
		// php 용 캐시파일
		@file_put_contents($this->get_cache_file_path($id), gzcompress(serialize(array('time' => time() + $sec, 'contents' => $contents))));
		// javascript 용 캐시파일
		@file_put_contents($this->get_json_file_path($id), $contents);
		return $contents;
	}

	function get_cache_file_path($id)
	{
		return str_replace('.po', '.cache', $id);
	}

	function get_json_file_path($id)
	{
		return str_replace('.po', '.json', $id);
	}

	function __($s)
	{
		return isset($this->i18n_data[$s]) && $this->i18n_data[$s] ? $this->i18n_data[$s] : $s;
	}
}

$i18n = new i18n();
define('LANG', $i18n->get_i18n_lang());


function __($s)
{
	global $i18n;
	return $i18n->__($s);
}

function _e($s)
{
	echo __($s);
}