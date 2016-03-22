QOX ExEngine Application Framework
==================================
ExEngine PHP Opensource Framework

Git nightly repository.

Homepage: (under development http://oss.qox-corp.com/exengine)

Quick Start MVC Application
===========================

MVC-ExEngine application files has ben sepparated to a different project:

https://github.com/QOXCorp/mvc-exengine

Note: The access to controllers is this way:
http://localhost/myapp/index.php/CONTROLLER_NAME/FUNCTION_NAME/PARAMETER1/PARAMETER2/?GET1=VAL&GET2=VAL

FUNCTION_NAME is a function inside the controller class.
PARAMETER1, PARAMETER2... are the parameter or parameters of the function (if has parameters), the first one will be the first one of the function too.
GET1, etc. are the standard GET method values.

You can also pass POST values to a function inside a controller.

See the examples dir for more detail about resources, views and models.

Quick Start Application
=======================
- Create a folder for you app.
- Create a folder for libraries (ie. libs) and inside it create a folder for ExEngine, (ie. ee).
- Copy all files to that folder (download as zip or submodule it).
- Edit "libs/ee/eefx/cfg.php" and set correctly the path to the ExEngine install, modify the following line:

```php
	[...]
		"http_path" => "/myapp/libs/ee/"
	[...]
```
You can also modify the default database array in order to use ExEngine Database Manager.
- Start using ExEngine in your application files:

```php
	<?php
		include_once("libs/ee/ee.php");
		$ee = new exengine();

		$str1 = "hello world";
		$str2 = "world";

		if ($ee->strContains($str1,$str2))
			echo $str2 . ' is in ' . $str1 ;
		else
			echo 'What?';
	?>
```

To-Do
=====
- Documentation
- Homepage

How To Install
==============
If not using Git Tools, just click ZIP or TAG.GZ icon at the top of the page to get the latest nightly release.
Remember that EE7 is an alpha product, but the releases contains minimal bugs, and is ready to development builds of your project.

Remember that when changing version numbers (or revision) may be minor or mayor changes that can affect the desired function of your product.

The release of this product is under the GPL license, so its use is at your own risk. No warranties provided.

ExEngine Homepage: (under development  http://oss.qox-corp.com/exengine)

ExEngine Docs  : (under development  http://oss.qox-corp.com/docs/exengine)

License Info
------------

MIT License

Copyright (c) 2016 QOX Corporation

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

(C) 2016 QOX Corporation - qox-corp.com