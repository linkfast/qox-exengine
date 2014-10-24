<?php

namespace ExEngine\MVC;

class WebTool {

    private $ee;

    function __construct(DefaultApplicationConfig $applicationConfig) {
        $this->ee = &ee_gi();
        $this->AppCfg = $applicationConfig;
    }

    function run() {

        if (isset($_GET['F'])) {
            $F = $_GET['F'];
            if (method_exists($this,$F))
                $this->$F();
            else {
                header('Location: /');
            }
        } else {
          $this->index();
        }

    }

    private function asset() {
        $A = $_GET['A'];
        $R = $_GET['R'];
        if ($this->ee->strContains($A,'css')) {
            header('content-type: text/css');
        }
        if ($this->ee->strContains($A,'js')) {
            header('content-type: text/javascript');
        }
        if ($this->ee->strContains($A,'otf')) {
            header('content-type: font/opentype');
        }
        $file = $this->ee->libGetResPath($R) . $A;
        print file_get_contents($file);
    }

    private function index() {
        $this->loadView('index.phtml', ['Title' => 'MVC-ExEngine Tool Web Interface']);
    }

    private function new_model_act() {
        $Name = $_POST['name'];
        $Props = $_POST['properties'];
        $Namespace = $_POST['namespace'];
        ob_start();
        system('mvctool -g model ' . $Name . ' "' . $Props . '" ' . $Namespace);
        $Output = ob_get_contents();
        ob_end_clean();

        $this->loadView('result.phtml', [
            'Title' => 'Function result',
            'Output' => $Output]);
    }

    private function new_model() {
        $this->loadView('new_model.phtml', [
            'Title' => 'New Model'
        ]);
    }

    private function new_controller_act() {
        $Name = $_POST['name'];
        ob_start();
        system('mvctool -g controller ' . $Name);
        $Output = ob_get_contents();
        ob_end_clean();

        $this->loadView('result.phtml', [
            'Title' => 'Function result',
            'Output' => $Output]);
    }

    private function new_controller() {
        $this->loadView('new_controller.phtml', [
            'Title' => 'New Controller'
        ]);
    }

    private function new_dbo() {
        $Name = $_POST['name'];
        $Driver = $_POST['driver'];
        $DBCfg = $_POST['dbcfg'];
        $Props = $_POST['properties'];
        $Namespace = $_POST['namespace'];

        ob_start();
        system('mvctool -g model_dbo ' . $Name . ' ' . $Driver . ' "' . $Props . '" ' . $DBCfg . ' ' . $Namespace);
        $Output = ob_get_contents();
        ob_end_clean();

        $this->loadView('result.phtml', [
            'Title' => 'Function result',
            'Output' => $Output]);

    }

    private function new_model_dbo() {

        $CfgFiles = [];

        if ($handle = opendir($this->AppCfg->ConfigurationFolder.'/database')) {
            while (false !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'yml')
                {
                    $CfgFiles[] = str_replace('.yml','',$file);
                }
            }
            closedir($handle);
        }

        $this->loadView('new_model_dbo.phtml', [
            'Title' => 'New DBO Model',
            'CfgFiles' => $CfgFiles
        ]);
    }

    private function loadView($V, $Data=[]) {
        extract($Data);

        $View = $this->ee->libGetResPath('mvc-ee') . 'webtool/views/' . $V;
        $Container = $this->ee->libGetResPath('mvc-ee') . 'webtool/views/' . 'layout.phtml';

        ob_start();
        include_once($View);
        $View_Data = ob_get_contents();
        ob_end_clean();

        include_once($Container);
    }
}

?>