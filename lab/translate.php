<?php

// 쩝 노가다... 하기 실어 만들라 했더니 차단 당했당. .. 그런데 크롬브라우저에서는 아직도 되는걸로 봐서는 IP 차단은 아닌것 같고 ... 쿠키나 세션인것 같은데..... 세션보다는 쿠키기반인것 같다..

// 아!!!! 노가다 싫다 자동 구굴 번역기 만들자!!!
include 'library.php';

$language_code = 'km';

$testing = 1; // 디버그 할때 사용.

// po 파일 열기
$pofile = './'.$language_code.'/LC_MESSAGES/WWW.po';
$data = $i18n->parse_po($pofile);
// var_dump($data);


// 번역 끝났으면 저장해야지!!  아니 먼저 저장할 파일 만들고... 
$header = '# SOME DESCRIPTIVE TITLE.
# Copyright (C) YEAR THE PACKAGE\'S COPYRIGHT HOLDER
# This file is distributed under the same license as the PACKAGE package.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
msgid ""
msgstr ""
"Project-Id-Version: \n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: 2020-06-03 01:16+0900\n"
"PO-Revision-Date: 2020-06-03 01:17+0900\n"
"Last-Translator: \n"
"Language-Team: \n"
"Language: '.$language_code.'\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Generator: Poedit 2.0.6\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\n"
"X-Poedit-SourceCharset: UTF-8\n"';

$trfile = './'.$language_code.'/LC_MESSAGES/WWW.po.tr';
file_put_contents($trfile, $header); //FILE_APPEND

// 데이타 파싱은 끝났으니 번역을 하자... 무료로... 
// https://github.com/Stichoza/google-translate-php
include 'vendor/autoload.php';
use Stichoza\GoogleTranslate\GoogleTranslate;
$tr = new GoogleTranslate($language_code); // Translates into English .. 
$tr->setOptions(['headers' => ['User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36']]);
$tr->setUrl('https://translate.google.com/translate_a/single'); 
$tr->setSource(); // Detect language automatically
// $tr->translate('Hello World!');
$n=0; // 2 tps (translate per seconds) 속도로 요청하도록 하기 위함.
foreach($data as $key => $val) {
	$str = trim($val) ? $val : $key;
	if(!$key) {continue;}
	
	// 번역
	$str = $tr->translate($str);

	$con = '

msgid "'.str_replace('"','\"', $key).'"
msgstr "'.str_replace('"','\"', $str).'"';
	// 번역 내용 저장
	file_put_contents($trfile, $con, FILE_APPEND);

	// 진행상황표시
	echo $n.',';

	// 구글 IP당 요청수 회피하기.
	// if($n && $n%2==0) {
		sleep(1);
	// }
	$n++;
	if($testing && $n>10) {
		var_dump($data[$key], $key, $val);exit('번역 테스트 끝');
	}
}

exit('끝');
