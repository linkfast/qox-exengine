<?php
/**
 *
 */

namespace ExEngine\MVC;

class Tool {
	const VERSION = '0.0.0.1';
	private static $instance;
	private $ApplicationConfig;
	public static function &get_instance()
	{
		return self::$instance;
	}

	function __construct(DefaultApplicationConfig $applicationConfig) {
		$this->ee = &ee_gi();
		$this->ApplicationConfig = $applicationConfig;
		$this->ApplicationConfig->ApplicationInit();
		$this->ee->eeLoad('eespyc');
		$SW = new \eespyc();
		$SW->load();
		self::$instance =& $this;
	}

	function run() {
		global $argv;
		if(defined('STDIN')) {
			$this->fromCli = true;
			$this->RenderMode = "cli";
		} else {
			$this->fromCli = false;
			$this->ee->errorExit('MVC-ExEngine : Tool','This tool cannot be run as a web application.<br><br>Open a terminal and write <b>php mvctool.php</b> to start.<br><br>');
		}

		if (isset($argv)) {
			if (in_array("--help",array_map('strtolower', $argv))) {
				echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
				echo "Arguments: \n";
				echo "\t-g\tEnable the generator capability, must be followed by one of these options.\n";
				echo "\t\tcontroller\tCreates a new controller.\n";
				echo "\t\tmodel\t\tCreates a new model.\n";
				echo "\t\tmodel_dbo\tCreates a new DBO model (CRUD).\n";
				echo "\t-db\tEnable the database options, must be followed by the name of the database config\n";
				echo "\t\tfile name without the extension (ex. default), then you can add the following\n";
				echo "\t\tcommands.\n";
				echo "\t\tcreate\tCreates the configuration schema.\n";
				echo "\t\ttables\tCreates or updates the tables in the database (depends on the DBO models).\n";
				echo "\t\t\tThis option does not support MongoDB (tables are created in the first use automatically).\n";
				echo "\n";
				echo "Misc. Options:\n";
				echo "\t-ct\tEnables Console Coloring (only if you have a color capable terminal).\n";
				exit();
			}
			if (in_array("-ct",array_map('strtolower', $argv))) {
				$this->RenderMode = "colorcli";
			}
		}

		switch ($argv[1]) {
			case "-g":
				if (isset($argv[2]) and strlen($argv[2])>0) {
					switch ($argv[2]) {
						case "controller":
							if (isset($argv[3]) and strlen($argv[3]) > 0) {
								echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
								echo "\nCreating controller `".ucfirst($argv[3])."`...\n";
								$this->createController($argv[3]);
								echo "Finished.\n";
							} else {
								echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
								echo "\nThe `-g controller` command requires the controller name, check the following\n";
								echo "example:\n\n";
								echo "\tmvctool -g controller mynewcontroller\n\n";
							}
							break;
					}
				} else {
					echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
					echo "\nThe `-g` command requires an additional command, check the following\n";
					echo "examples:\n\n";
					echo "\tmvctool -g model mynewmodel\n";
					echo "\tmvctool -g controller mynewcontroller\n\n";
				}
				break;
			case "-db":

				break;
		}



	}

	protected function createController($ControllerName) {
		$ControllerSnippet = '<?php
/**
* MVC-ExEngine
* This controller was generated using MVCTool.
* Date: '.strftime("%d/%m/%Y").'
* Timestamp: '.time().'
*/
	class ' . ucfirst($ControllerName) . ' extends \\ExEngine\\MVC\\Controller {
		/* Set this var to true when writing Ajax servers, it will not load the layout */
		var $imSilent = false;

		/* You can set a different layout than the default one setting this variable */
		var $layout = "default";

		/* You can pass additional data to the layout with this variable, remember its an associative array */
		var $layoutData = [];

		/* You can set the default locale for this controller, set to "default" to use the application default. */
		var $locale = "default";

		/* You can set here the controller init (will be executed always) */
		protected function __atconstruct() {
			/* write your controller init code here */
		}

		/* this is the default action */
		function index() {
			/* write your code here */
			print "<h1>Hello World!</h1>";
		}

		/* Add more actions after this, to use actions just append the action name to the url, check the action3 example for more information
		function action2() {
			# write your code here
			print "<h1>This is action2</h1>";
		}

		function action3($arg1=null) {
			# this action has arguments, you can add more arguments to this action, they will be passed separating each one with `/` in the url,
			# for ex. to pass arg1, you should call this action like this: /'.strtolower($ControllerName).'/action3/argument1, so $arg1 will contain the
			# argument1 string.
			print "<h1>$arg1</h1>";
		}
		*/
	}
?>';
		if (file_exists($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ControllersFolder) and
			is_dir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ControllersFolder)) {
			file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ControllersFolder . '/' . strtolower($ControllerName) . '.php',$ControllerSnippet,LOCK_EX);
		} else {
			$this->ee->errorExit('MVC-ExEngine Tool','Controllers folder does not exist, check Application Configuration.');
		}
	}

	protected function createSchema() {

	}

	protected function createModel($ModelName) {

	}

}
?>