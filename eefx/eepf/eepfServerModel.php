<?
session_start();
# ExEngine Portal Framwork / EEPF Server Model

/*
	This file is part of ExEngine7.

    ExEngine7 is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Foobar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

#This is the object creation part, you should add arguments if you need or change configuration for your Application.

include_once("../../ee/eefx/eepf/ee7.php");
$arg["SilentMode"] = true;
$ee = new exengine($arg);
$eepf = new eepf($ee);

#This will create a Server here, no more changes are need.

$eepf->thisEEPFServer();

?>