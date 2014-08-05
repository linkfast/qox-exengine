QOX MVC-ExEngine
================

Use this .htaccess file in the root folder of your application (same place as index.php),
this will enable rewrite rules for MVC-ExEngine.

You must enable rewrite compatibility in MVC-ExEngine, setting the 
rewriteRulesEnabled property to true.

Example:
$ee = new exengine();
$mvc = new eemvc_index("start");

$mvc->rewriteRulesEnabled = true; # NOTE THIS PROPERTY CHANGE, by default is false.

$mvc->start();