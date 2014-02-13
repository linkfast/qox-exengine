<?php
	// Controller class name (Capitalized) must the same as the filename (lowercase).
	class Start extends eemvc_controller {
	
		function index() {
			$this->loadView("helloworld.html");
		}

	}
?>