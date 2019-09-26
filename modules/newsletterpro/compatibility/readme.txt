Before to proceed this, ask the developer about this problems.

1. Fix database errors on Prestashop 1.5.0.1 - 1.5.1.0

-- Override class functions override/classes/db/Db.php with the functions from this file db_prestashop_v1501_1510/Db.php for prestashop 1.5.0.1 - 1.5.1.0

2. Fix database cache error if you have this error:


Fatal error:  Uncaught Cannot execute queries while other unbuffered queries are active.Consider using PDOStatement::fetchAll().  Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.

-- Override class functions override/classes/db/Db.php with the functions from this file db_cache_fix/Db.php.

3. Font Awesome Not Displaying in older versions of firefox, fix:

-- Add the content to the .htaccess root file
<FilesMatch ".(ttf|otf|eot|woff)$">
  <IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "*"
  </IfModule>
</FilesMatch>

or 

<FilesMatch ".(ttf|otf|eot|woff)$">
	<ifModule mod_headers.c>
	   Header set Access-Control-Allow-Origin: *
	</ifModule>
</FilesMatch>
