<?php

class Renderer_clihtml {	

	static function loadRq() {
		$ee = &ee_gi();
		$ee->eeLoad("clicolors");
		$cc = new clicolors($ee);
		$cc->load();	
	}
	
	static function colorcli($Text) {		
		$c = new \Colors\Color();
		return 	$c($Text)->colorize();
	}

	static $ori = array(
		"<tab>",
		"<br/>",
		"<b>","</b>",
		"<i>","</i>",
		"<green>","</green>",
		"<red>","</red>"
	);

	static $html = array(
		"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
		"<br/>\n",
		"<b>","</b>",
		"<i>","</i>",
		'<span style="color: green">',"</span>",
		'<span style="color: red">',"</span>"
	);
	static $cli = array(
		"\t",
		"\n",
		"","",
		"","",
		"","",
		"","");
	static $colorcli = array(
		"\t",
		"\n",
		"<bold>","</bold>",
		"<italic>","</italic>",
		"<green>","</green>",
		"<red>","</red>"
		);
}

?>