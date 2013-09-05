ExEngine 7
==========

ExEngine 7 PHP Open Source Framework

Git nightly repository.

Homepage: (under development)
 
Quick Start MVC Application
===========================

1. Create a folder for you app.
2. Create a folder for libraries (ie. libs) and inside it create a folder for ExEngine7, (ie. ee).
3. Copy all files to that folder (download as zip or clone).
4. Create an index.php file that will serve all calls in the root of your app.
5. Copy this code into that file, and modify paths if necessary.
```php
	<?php
		include_once("libs/ee/ee.php");
		$ee = new exengine(array("SilentMode" => true));
		$mvc = new eemvc_index($ee,"start");
		$mvc->SessionMode = true;
		$mvc->start();
	?>
```
6. Create these folders: models, views, controllers and static in the root of your app.
7. Create a new Controller to test the install, create a file called "start.php" inside the controllers folder.
8. Copy this code into start.php:
```php
	<?php
		class Start extends eemvc_controller {
			
			function index() {
				print '<h1> Hello World! </h1>';
			}
		
		}
	?>
```
9. Edit "libs/ee/eefx/cfg.php" and set correctly the path to the ExEngine install, modify the following line:
	```php
	[...]
		"http_path" => "/myapp/libs/ee/"
	[...]
	```
10. Access your app through a web browser. (i.e. http://localhost/myapp/) .

Note: The access to controllers is this way:
	http://localhost/myapp/index.php/CONTROLLER_NAME/FUNCTION_NAME/PARAMETER1/PARAMETER2/?GET1=VAL&GET2=VAL
	
	FUNCTION_NAME is a function inside the controller class.
	PARAMETER1, PARAMETER2... are the parameter or parameters of the function (if has parameters), the first one will be the first one of the function too.
	GET1, etc. are the standard GET method values.

	You can also pass POST values to a Function inside a Controller.
	
To-Do
=====

	- Unit Testing
	- Documentation

How To Install
==============

If not using Git Tools, just click ZIP icon at the top of the page to get the latest nightly release.
Remember that EE7 is an alpha product, but the releases contains minimal bugs, and is ready to development builds of your project.

Remember that when changing version numbers (or revision) may be minor or mayor changes that can affect the desired function of your product.

The release of this product is under the GPL license, so its use is at your own risk. No warranties provided.

ExEngine Homepage: (under development)

ExEngine 7 Wiki  : (under development)

(C) 2013 LinkFast Company (www.linkfastsa.com)