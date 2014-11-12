<?php
/**
@file oracle.edbl.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@gmail.com>
@version 0.0.1.1

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

ExEngine / Database Manager Linker / Oracle

 */

class edbl_oracle
{
    #EDBL Standard Version
    const _edbl_sv = "1.0.0.0";

    #This driver version
    const _edbl_dv = "0.0.1.0";

    #Constructor eval code
    const _edbl_c = 'edbl_oracle($this,$this->ee,$EDBL_Special)';

    #Database mode, socket or file_mode
    const _edbl_cr = 'socket';


    # EDBL DRIVER
    /* @var $dbm ExEngine\DatabaseManager */
    private $dbm;

    private $ee;

    function __construct($dbm,$ee,$EDBL_Special) {
        $this->dbm = &$dbm;
        $this->ee = &ee_gi();
        if (!function_exists('oci_connect')) {
            $this->ee->errorExit('ExEngine Oracle Linker', 'OCI library functions are not found, please enable php_oci_11g module in php.ini.');
        }
    }

    function query($query,$edbl_options=null) {
        $r = oci_parse($this->dbm->connObj, $query);
        oci_execute($r);

        if (isset($this->dbm->aDbSettings["Oracle_AutoCommit"]) and
            $this->dbm->aDbSettings["Oracle_AutoCommit"]) {
            oci_commit($this->dbm->connObj);
        }

        return $r;
    }

    function fetchArray($query_obj,$edbl_options=null) {

        if ($edbl_options==null){
            $edbl_options[0] = OCI_BOTH;
        }

        return oci_fetch_array($query_obj,$edbl_options[0]);
    }

    function open() {
        //  [//]host_name[:port][/service_name][:server_type][/instance_name]
        $SessionMode = null;
        if (isset($this->dbm->aDbSettings["Oracle_SessionMode"])) {
            eval('$SessionMode = ' . $this->dbm->aDbSettings["Oracle_SessionMode"] . ";");
        }
        return oci_connect($this->dbm->user,$this->dbm->passwd,$this->dbm->host, null, $SessionMode);
    }

    function close() {
        return oci_close($this->dbm->connObj);
    }
}
?>