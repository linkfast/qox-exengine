MVC-ExEngine Database Object Driver
===================================

Feel free to create your own DBO drivers and place them in this folder, follow the MV_DBO_MYSQL or MV_DBO_MONGODB model.

Remember, you must create the following methods for your DBO driver:

	load, load_all, load_values, search, insert, delete, update, __toString, __toArray.

You can create more methods but is not recommended.

All of them must have default arguments defined and working.

The following properties are key values to set the TABLEID (table name) and INDEXKEY (identifier property):

	TABLEID, INDEXKEY

You can create more required properties, set their names in uppercase, checkout the MV_DBO_MONGODB that has the MONGODB property that specifies wich database is going to be used. MySQL DBO does not have that field because is using the same database connection of MVC-ExEngine that uses the specified database in the ExEngine instance (ExEngine Database Manager).