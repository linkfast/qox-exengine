<? 
// ENGINE DATA
$engine_name = "SoundManager2 for ExEngine";
$engine_version = "EEN: 1.0.31102008 / SM2: 2.90a.28102008";
$engine_author = "Giancarlo Chiappe Aguilar";
$engine_alias = "soundmanager2";
//

//EXENGINE VERSION CHECK
if ($exen_loader != 1) {
	$exen_silent = 1 ;
	include_once("../../../../ee/eefx/ee6clayer/ex_engine.php");
}
if (exen_checkversion("5.5") === true) {
//EXENGINE VERSION CHECK

function soundmanager2_script() {
	global $exen_config;
	return $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/soundmanager2/script/soundmanager2-nodebug-jsmin.js" ;
}


function soundmanager2_scriptd() {
	global $exen_config;
	return $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/soundmanager2/script/soundmanager2.js" ;
}

function soundmanager2_init() {
	global $exen_config;
	print '<!-- SM2 // EXENGINE -->
<script type="text/javascript">
soundManager.url = \''.$exen_config["http_path"].$exen_config["ex_path"]."ex_framework/soundmanager2/".'\';
soundManager.debugMode = false;
</script>
<!-- SM2 // EXENGINE -->';
	}
	
function soundmanager2_initd() {
	global $exen_config;
	print '<!-- SM2 // EXENGINE -->
<script type="text/javascript">
soundManager.url = \''.$exen_config["http_path"].$exen_config["ex_path"]."ex_framework/soundmanager2/".'\';
</script>
<!-- SM2 // EXENGINE -->';
	}

//
}
//
?>