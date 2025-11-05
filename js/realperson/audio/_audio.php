<?php
	/*
	Copyright (c) 2019 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single web site may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/
	
	/* Decode String */
	$encoded = filter_input(INPUT_GET, 'encoded');
	$decoded = '';
	for($i = 0; $i < strlen($encoded); $i++) {
		$b       = ord($encoded[$i]);
		$a       = $b ^ 123;
		$decoded .= chr($a);
	}
	$decoded = substr(substr($decoded, 0, strlen($decoded) - 9), 4);
	
	/* Set Map */
	$map = array(
		md5('a') => 'a.mp3',
		md5('b') => 'b.mp3',
		md5('c') => 'c.mp3',
		md5('d') => 'd.mp3',
		md5('e') => 'e.mp3',
		md5('f') => 'f.mp3',
		md5('g') => 'g.mp3',
		md5('h') => 'h.mp3',
		md5('i') => 'i.mp3',
		md5('j') => 'j.mp3',
		md5('k') => 'k.mp3',
		md5('l') => 'l.mp3',
		md5('m') => 'm.mp3',
		md5('n') => 'n.mp3',
		md5('o') => 'o.mp3',
		md5('p') => 'p.mp3',
		md5('q') => 'q.mp3',
		md5('r') => 'r.mp3',
		md5('s') => 's.mp3',
		md5('t') => 't.mp3',
		md5('u') => 'u.mp3',
		md5('v') => 'v.mp3',
		md5('w') => 'w.mp3',
		md5('x') => 'x.mp3',
		md5('y') => 'y.mp3',
		md5('z') => 'z.mp3'
	);
	
	/* Check File */
	if(!is_file($map[$decoded])) {
		include_once($_SERVER['DOCUMENT_ROOT'] . '/404.php');
		exit();
	}
	
	/* Structure Header */
	header('Cache-Control: private, max-age=0, must-revalidate');
	header('Content-Type: audio/mpeg, audio/mp3');
	header('Content-Length: ' . filesize($map[$decoded]));
	
	/* Read File */
	readfile($map[$decoded]);