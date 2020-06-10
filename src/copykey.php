<?php

// 자동 번역은 노가다 하는데 그나마 편하게 하기 위해 번역값을 키로 만들두기. 그럼 단축키와 검색으로 노가다가 쉬워짐.
// msgstr "(.*)" -> Ctrl + Shift + t

// 아!!!! 노가다 싫다 자동 구굴 번역기 만들자!!!
include 'library.php';

$language_code = 'uz';

$testing = 0; // 디버그 할때 사용.

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

$trfile = './'.$language_code.'/LC_MESSAGES/WWW.po.new';
file_put_contents($trfile, $header); //FILE_APPEND

$n=0; 
foreach($data as $key => $val) {
	$str = trim($val) ? $val : $key;
	if(!$key) {continue;}
	
	// 번역 내용 저장
	$con = '

msgid "'.$key.'"
msgstr "'.$str.'"';
	file_put_contents($trfile, $con, FILE_APPEND);

	// 진행상황표시
	echo $n.',';
	$n++;
	if($testing && $n>10) {
		var_dump($data[$key], $key, $val);exit('테스트 끝');
	}
}

rename($pofile, './'.$language_code.'/LC_MESSAGES/WWW.'.date('YmdHis').'.po');
rename($trfile, './'.$language_code.'/LC_MESSAGES/WWW.po');

exit('끝');
