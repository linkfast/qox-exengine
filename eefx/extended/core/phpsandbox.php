<?php
/**
@file phpsandbox.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@gmail.com>
@version 1.0.0.0

@section LICENSE

ExEngine is free software; you can redistribute it and/or modify it under the
terms of the GNU Lesser Gereral Public Licence as published by the Free Software
Foundation; either version 2 of the Licence, or (at your opinion) any later version.
ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
without even the implied warranty of merchantability or fitness for a particular purpose.
See the GNU Lesser General Public Licence for more details.

You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.

@section DESCRIPTION

Wrapper for https://github.com/Corveda/PHPSandbox.

@section TODO

Add some special functions exclusive to ExEngine about Corveda PHP Sandbox.

@section LICENSE

Custom Corveda License https://github.com/Corveda/PHPSandbox/blob/master/LICENSE

@section INSTALLATION

PHPSandbox from Corveda requires Composer, init composer and require corveda/php-sandbox.

 */

class corveda_phpsandbox {
    /* @var $ee \ExEngine\Core */
    private $ee;

    var $isFromComposer = false;
    var $installed = false;
    var $composer = null;

    function __construct($ee=null) {
        if ($ee == null)
            $this->ee = &ee_gi();
        else
            $this->ee = &$ee;
        $this->checkInstall();
    }

    private function checkInstall() {
        $this->ee->eeLoad('composer');
        $this->composer = new \ExEngine\Extended\Composer();
        if ($this->composer->composerDetected()) {
            if ($this->composer->isPackageInstalled('corveda/php-sandbox')) {
                $this->composer->autoload();
                $this->installed = true;
                $this->isFromComposer=true;
            }
        }
    }
    function load() {
        if ($this->installed) {
          if (!$this->isFromComposer) {
              return false;
          } else
              return true;
        }
    }
}