<?php

class pokemon extends eemvc_model_dbo_mongodb {
	var $_mongo_id; // create this var to use the mongodb [$id] object.

	// some vars...
	var $no;
	var $name;
	var $type;
	var $desc;

	var $not_in_db_var;

	var $MONGODB = "pokedexds"; // database name
	var $TABLEID = "pokemon"; // collection name
	var $EXCLUDEVARS = array ( "not_in_db_var" );
	var $INDEXKEY = "_mongo_id"; // index name, if unique index not defined in collection, set to _mongo_id to use the [$id] object.

}

?>