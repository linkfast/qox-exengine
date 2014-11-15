<?php
/**
 *
 */

namespace ExEngine\MVC;

class Tool {
	const VERSION = '0.0.0.5';
	private static $instance;
	private $ApplicationConfig;
    private $MVC_Index;
	public static function &get_instance()
	{
		return self::$instance;
	}

	function __construct(DefaultApplicationConfig $applicationConfig) {
		$this->ee = &ee_gi();
		$this->ApplicationConfig = $applicationConfig;
        $this->ApplicationConfig->UsingFromCLI = true;
		$this->ee->eeLoad('eespyc');
		$SW = new \eespyc();
		$SW->load();
        $this->MVC_Index = new Index($this->ApplicationConfig);
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
                echo "(C) QOX Corporation <qox-corp.com>" . "\n";
				echo "Arguments: \n";
                #echo "\t-sf\tEnable scaffolding.";
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
                echo "\t-wu\tEnables web interface (experimental) (use '-wu online' for network access).\n";
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
                                echo "(C) QOX Corporation <qox-corp.com>" . "\n";
								echo "\nCreating controller `".ucfirst($argv[3])."`...";
								$this->createController($argv[3], $argv[4], $argv[5]);
								echo "Finished.\n";
							} else {
								echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
								echo "\nThe `-g controller` command requires the controller name, check the following\n";
								echo "example:\n\n";
								echo "\tmvctool -g controller mynewcontroller\n\n";
							}
							break;
                        case "model":
                            if ((isset($argv[3]) and isset($argv[4])) and
                                (strlen($argv[3]) > 0 and strlen($argv[4]) > 0)) {
                                echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                                echo "(C) QOX Corporation <qox-corp.com>" . "\n";
                                echo "\nCreating Model `".ucfirst($argv[3])."`...";
                                $this->createModel($argv[3], $argv[4], $argv[5]);
                                echo "Finished.\n";
                            } else {
                                echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                                echo "(C) QOX Corporation <qox-corp.com>" . "\n";
                                echo "\nThe `-g model` command requires the model name and class properties check the following\n";
                                echo "example:\n\n";
                                echo "\tmvctool -g model mymodel properties [subfolder/namespace]\n";
                                echo "\tproperties should be in this format: property1,property2=defvalue\n";
                                echo "\texample:  \"title,content,age=10\"\n";
                                echo "\tAlso you can bypass optional parameters order by setting them to null.\n";
                            }
                            break;
                        case "model_dbo":
                            if ((isset($argv[3]) and isset($argv[4]) and isset($argv[5])) and
                                (strlen($argv[3]) > 0 and strlen($argv[4]) > 0 and strlen($argv[5]) > 0)) {
                                echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                                echo "(C) QOX Corporation <qox-corp.com>" . "\n";
                                echo "\nCreating DBO Model `".ucfirst($argv[3])."`...";
                                $this->createModelDBO($argv[3], $argv[4], $argv[5], $argv[6], $argv[7], $argv[8]);
                                echo "Finished.\n";
                            } else {
                                echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                                echo "\n(C) QOX Corporation <qox-corp.com>" . "\n";
                                echo "\nThe `-g model_dbo` command requires the model name, dbo driver and class properties check the following\n";
                                echo "example:\n\n";
                                echo "\tmvctool -g model_dbo mymodel dbo_driver properties [database config filename] [subfolder/namespace] [table_id]\n";
                                echo "\tproperties should be in this format: property1=type;defaultvalue,property2=type\n";
                                echo "\ttypes can be: index (for the primary key, its int on sql databases)\n";
                                echo "\t              int, string and date. ints are always int32 and string are text in mysql and sqlite.\n";
                                echo "\texample:  \"_mongo_id=index,title=string;DefaultValue,order=int,15\"\n";
                                echo "\tAlso you can bypass optional parameters order by setting them to null.\n";
                            }
                            break;
					}
				} else {
					echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                    echo "(C) QOX Corporation <qox-corp.com>" . "\n";
					echo "\nThe `-g` command requires an additional command, check the following\n";
					echo "examples:\n\n";
					echo "\tmvctool -g model mynewmodel\n";
					echo "\tmvctool -g controller mynewcontroller\n\n";
				}
				break;
			case "-db":

				break;
            case "-wu";
                echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                echo "(C) QOX Corporation <qox-corp.com>" . "\n";                
				if (isset($argv[2]) and $argv[2]=="online") {
					echo "\nStarting web interface: http://localhost:8989/.\nPress CTRL+C to stop serving.";
					system('php -S 0.0.0.0:8989 bin/webui.php');
				} else {
					echo "\nStarting web interface: http://*:8989/.\nPress CTRL+C to stop serving.";
					system('php -S localhost:8989 bin/webui.php');				
				}
                break;
			default:
				echo "\nMVC-ExEngine v." . Index::VERSION . " Tool v." . self::VERSION . "\n";
                echo "\n(C) QOX Corporation <qox-corp.com>" . "\n";
				echo "Invalid option. \n";
				echo "Run with the --help argument for more info. \n";
				break;
		}



	}

    protected function createModelDBO($ModelName, $DBO_Driver, $Params, $ConnectionConfig="default", $Namespace="null", $Table_Id="null") {

        if (strlen($ConnectionConfig) == 0 or $ConnectionConfig == "null")
            $ConnectionConfig = "default";

        if (strlen($Namespace) == 0)
            $Namespace = "null";

        if (strlen($Table_Id) == 0)
            $Table_Id = "null";

        $DriverClassNames = [
            "mongodb" => "\\ExEngine\\MVC\\DBO\\MongoDB",
            "mysql" => "\\ExEngine\\MVC\\DBO\\MySQL",
            "sqlite" => "\\ExEngine\\MVC\\DBO\\SQLite"
        ];

        if (!array_key_exists($DBO_Driver,$DriverClassNames)) {
            echo "ERROR! - Invalid driver, valid drivers: mongodb, mysql, sqlite.\n";
            return;
        }

        if ($ConnectionConfig=="default") {
            $ConnectionConfig = $this->ApplicationConfig->DefaultDatabase;
        }

        $Create=true;
        if (!file_exists($this->ApplicationConfig->ConfigurationFolder . '/database/' . $ConnectionConfig . '.yml')) {
            echo "WARNING! - Database configuration does not exists, I'm not creating tables.\n";
            $Create = false;
        }

        $P = explode(',' , $Params);
        if (count($P) <= 1) {
            echo "ERROR! - Malformed or no properties for this DBO, please add at least two properties.\n";
            echo "         example:\n";
            echo "         mvctool -g model_dbo " . $ModelName . " " . $DBO_Driver . " \"_mongo_id=index,title=string;DefaultValue,order=int,15\"" . " " . $ConnectionConfig;
            return;
        }

        $N = "\n";
        if ($Namespace!="null") {
            $N = "\t" . 'namespace ' . $Namespace. ';' . "\n";
        }

        $PropertiesString = "\t\t" . 'var $DBC = "' . $ConnectionConfig . '";' . "\n";

        if ($Table_Id=="null")
            $Table_Id = strtolower($ModelName);

        $PropertiesString .= "\t\t" . 'var $TABLEID = "' . $Table_Id . '";' . "\n";

        $AtConstructString = "\t\t\t".'parent::__construct();' . "\n";
        foreach ($P as $Prop) {
            $PX = explode('=',$Prop);
            $Name = $PX[0];
            $DefVal=null;
            if ($this->ee->strContains($PX[1], ';')) {
                $DV = explode(';', $PX[1]);
                $Type = $DV[0];
                $DefVal = $DV[1];
            } else {
                $Type = $PX[1];
            }

            switch ($Type) {
                case 'index':
                        $PropertiesString .= "\t\t" . 'var $' . $Name . ';' . "\n";
                        $PropertiesString .= "\t\t" . 'var $INDEXKEY = "'.$Name.'";' . "\n";
                    break;
                case 'string':
                    if (strlen($DefVal) > 0) {
                        if (is_string($DefVal)) {
                            $PropertiesString .= "\t\t" . '/* String Property */' . "\n\t\t" . 'var $' . $Name . ' = "' . $DefVal . '";' . "\n";
                        } else {
                            echo "ERROR! - Invalid default value for '" . $Name . "', value must be String.\n";
                            return;
                        }
                    } else {
                        $PropertiesString .= "\t\t" . '/* String Property */' . "\n\t\t" . 'var $' . $Name . ';' . "\n";
                    }
                    break;
                case 'int':
                    if (strlen($DefVal) > 0) {
                        if (is_int($DefVal)) {
                            $PropertiesString .= "\t\t" . '/* Integer Property */' . "\n\t\t" . 'var $' . $Name . ' = ' . $DefVal . ';' . "\n";
                        } else {
                            echo "ERROR! - Invalid default value for '" . $Name . "', value must be Integer.\n";
                            return;
                        }
                    } else {
                        $PropertiesString .= "\t\t" . '/* Integer Property */' . "\n\t\t" . 'var $' . $Name . ';' . "\n";
                    }
                    break;
                case 'date':
                    if (strlen($DefVal) > 0) {
                        echo "ERROR! - Default value for date property is not supported.\n";
                        return;
                    } else {
                        $PropertiesString .= "\t\t" . '/* Date Property */' . "\n\t\t" . 'var $' . $Name . ';' . "\n";
                        if ($DBO_Driver=='mongodb') {
                            $AtConstructString .= "\t\t\t" . '$this->' . $Name . ' = new \MongoDate();' . "\n";
                        }
                    }
                    break;
                case 'object':
                    if ($DBO_Driver=='mongodb') {
                        if (strlen($DefVal) > 0) {
                            echo "ERROR! - Default value for object property is not supported.\n";
                            return;
                        } else {
                            $PropertiesString .= "\t\t" . '/* Object Property */' . "\n\t\t" . 'var $' . $Name . ';' . "\n";
                        }
                    } else {
                        echo "ERROR! - Object property is only supported in NoSQL databases.\n";
                        return;
                    }
                    break;
                default:
                    echo "ERROR! - Property type '".$Type."' not valid.\n";
                    return;
            }
        }

        $ModelDboSnippet = '<?php
/**
* MVC-ExEngine
* (C) '.strftime('%Y').' QOX Corporation <qox-corp.com>
* This DBO model was generated using MVCTool.
* Date: '.strftime("%d/%m/%Y - %H:%M:%S").'
* Timestamp: '.time().'
* Command: mvctool -g model_dbo '. $ModelName .' ' . $DBO_Driver . ' ' . $PropertiesString . ' ' . $ConnectionConfig . ' ' . $Namespace . ' ' . $Table_Id .'
*/
'.$N. "\t" . 'class '. ucfirst($ModelName) . ' extends ' . $DriverClassNames[$DBO_Driver] . ' {

'.$PropertiesString.'

'."\t\t".'function __construct() {
'.$AtConstructString.'
'."\t\t".'}
'."\t".'}
?>';

        if (file_exists($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder) and
            is_dir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder)) {
            if ( $Namespace!= "null" ) {
                if (file_exists($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace)) and
                    is_dir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace))) {
                    file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace) . '/' . strtolower($ModelName) . '.php',$ModelDboSnippet,LOCK_EX);
                } else {
                    mkdir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace));
                    file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace) . '/' . strtolower($ModelName) . '.php',$ModelDboSnippet,LOCK_EX);
                }
            } else {
                file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($ModelName) . '.php',$ModelDboSnippet,LOCK_EX);
            }
            print "OK\n";
        } else {
            $this->ee->errorExit('MVC-ExEngine Tool','Models folder does not exist, check Application Configuration.');
        }

    }

	protected function createController($ControllerName, $WithDefaults="defaults", $Commented="commented") {
        if (strlen($WithDefaults) == 0 or $WithDefaults == "defaults")
            $WithDefaults="defaults";

        if (strlen($Commented) == 0 or $Commented == "commented")
            $Commented="commented";


		$ControllerSnippet = '<?php
/**
* MVC-ExEngine
* (C) '.strftime('%Y').' QOX Corporation <qox-corp.com>
* This controller was generated using MVCTool.
* Date: '.strftime("%d/%m/%Y - %H:%M:%S").'
* Timestamp: '.time().'
* Command: mvctool -g controller '. $ControllerName .' ' . $WithDefaults . ' ' . $Commented .'
*/
	class ' . ucfirst($ControllerName) . ' extends \\ExEngine\\MVC\\Controller {' . "\n";

        if ($WithDefaults=="defaults") {
            if ($Commented=="commented")
                $ControllerSnippet .= '		/* Set this var to true when writing Ajax servers, it will not load the layout */' . "\n" ;
            $ControllerSnippet .= '		var $imSilent = false;' . "\n" ;

            if ($Commented=="commented")
                $ControllerSnippet .= '		/* You can set a different layout than the default one setting this variable */' . "\n" ;
            $ControllerSnippet .= '		var $layout = "default";' . "\n" ;

            if ($Commented=="commented")
                $ControllerSnippet .= '		/* You can pass additional data to the layout with this variable, remember its an associative array */' . "\n";
            $ControllerSnippet .= '		var $layoutData = [];' . "\n";

            if ($Commented=="commented")
                $ControllerSnippet .= '		/* You can set the default locale for this controller, set to "default" to use the application default. */' . "\n";
            $ControllerSnippet .= '		var $locale = "default";' . "\n";
        }



        if ($Commented=="commented")
            $ControllerSnippet .= '		/* You can set here the controller init (will be executed always) */' . "\n";

        $ControllerSnippet .= '		protected function __atconstruct() {' . "\n";

        if ($Commented=="commented")
            $ControllerSnippet .= '			/* write your controller init code here */' . "\n";

        $ControllerSnippet .= '		}' . "\n";

        //$ControllerSnippet .= '
        if ($Commented=="commented")
            $ControllerSnippet .= '		/* this is the default action */' . "\n";

        $ControllerSnippet .= '		function index() {' . "\n";
        if ($Commented=="commented")
            $ControllerSnippet .= '			/* write your code here */' . "\n";
        if ($WithDefaults=="defaults")
            $ControllerSnippet .= '			print "<h1>Hello World!</h1>";' . "\n";
        $ControllerSnippet .= '		}' . "\n";

        if ($WithDefaults=="defaults") {
            if ($Commented=="commented")
                $ControllerSnippet .= '		/* Add more actions after this, to use actions just append the action name to the url, check the action3 example for more information' . "\n";
            $ControllerSnippet .= '		function action2() {' . "\n";
                if ($Commented=="commented")
                    $ControllerSnippet .= '			# write your code here' . "\n";
                $ControllerSnippet .= '			print "<h1>This is action2</h1>";' . "\n";
            $ControllerSnippet .= '		}' . "\n";

        $ControllerSnippet .= '		function action3($arg1=null) {' . "\n";
                if ($Commented=="commented")
                    $ControllerSnippet .= '			# this action has arguments, you can add more arguments to this action, they will be passed separating each one with `/` in the url,
                # for ex. to pass arg1, you should call this action like this: /'.strtolower($ControllerName).'/action3/argument1, so $arg1 will contain the
                # argument1 string.' . "\n";

                $ControllerSnippet .= '			print "<h1>$arg1</h1>";' . "\n";
        $ControllerSnippet .= '		}
            */' . "\n";
        }
        $ControllerSnippet .= '	}
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

	protected function createModel($ModelName, $Params, $Namespace="null") {
        if (strlen($Namespace) == 0)
            $Namespace = "null";

        $P = explode(',' , $Params);
        if (count($P) <= 1) {
            echo "ERROR! - Malformed or no properties for this DBO, please add at least two properties.\n";
            echo "         example:\n";
            echo "         mvctool -g model " . $ModelName . " \"prop1=defval,prop2=defval\"\n";
            return;
        }

        $N = "\n";
        if ($Namespace!="null") {
            $N = "\t" . 'namespace ' . $Namespace. ';' . "\n";
        }

        $PropertiesString = "";

        foreach ($P as $Prop) {
            $DefVal=null;
            if ($this->ee->strContains($Prop, '=')) {
                $DV = explode('=', $Prop);
                $Name = $DV[0];
                $DefVal = $DV[1];
            } else {
                $Name = $Prop;
            }

            if (strlen($DefVal) > 0) {
                if (is_int($DefVal)) {
                    $PropertiesString .= "\t\t" . '/* Integer Property */' . "\n\t\t" . 'var $' . $Name . ' = ' . $DefVal . ';' . "\n";
                }
                elseif (is_string($DefVal)) {
                    $PropertiesString .= "\t\t" . '/* String Property */' . "\n\t\t" . 'var $' . $Name . ' = "' . $DefVal . '";' . "\n";
                }
            } else {
                $PropertiesString .= "\t\t" . '/* Property */' . "\n\t\t" . 'var $' . $Name . ';' . "\n";
            }
        }

        $ModelSnippet = '<?php
/**
* MVC-ExEngine
* This model was generated using MVCTool.
* Date: '.strftime("%d/%m/%Y - %H:%M:%S").'
* Timestamp: '.time().'
* Command: mvctool -g model '. $ModelName .' ' . $PropertiesString . ' ' . $Namespace .'
*/
'.$N. "\t" . 'class '. ucfirst($ModelName) . ' extends \\ExEngine\\MVC\\Model {

'.$PropertiesString.'

'."\t".'}
?>';

        if (file_exists($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder) and
            is_dir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder)) {
            if ( $Namespace!= "null" ) {
                if (file_exists($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace)) and
                    is_dir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace))) {
                    file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace) . '/' . strtolower($ModelName) . '.php',$ModelSnippet,LOCK_EX);
                } else {
                    mkdir($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace));
                    file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($Namespace) . '/' . strtolower($ModelName) . '.php',$ModelSnippet,LOCK_EX);
                }
            } else {
                file_put_contents($this->ApplicationConfig->AppFolder . '/' . $this->ApplicationConfig->ModelsFolder . '/' . strtolower($ModelName) . '.php',$ModelSnippet,LOCK_EX);
            }
            print "OK\n";
        } else {
            $this->ee->errorExit('MVC-ExEngine Tool','Models folder does not exist, check Application Configuration.');
        }
	}

}
?>