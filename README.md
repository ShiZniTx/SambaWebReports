# SambaWebReports
SambaPOS Web Interface for Reports using PHP

# What you will need for setting this up:
1. SambaPOS3,4,5
You can download it from here: http://www.sambapos.com

2. SQL Server 2012 or later version (possible to work with lower versions too)
You can download it from here: https://www.microsoft.com/en-us/download/details.aspx?id=29062
Also a tutorial for migrating and installing SQL Server 2012 can be found here:
https://forum.sambapos.com/t/how-to-migrate-from-sql-compact-edition-sdf-file-to-sql-server-2012-express/752

3. Wamp Server
You can download it from here: http://www.wampserver.com/en/

4. Microsoft Drivers for PHP for SQL Server
You can download it from here: https://www.microsoft.com/en-us/download/details.aspx?id=20098
I used version 3.2 which is compatible with PHP 5.4 and later till PHP 7
After installing you can include the extension by adding these lines to php.ini ( in case you are using PHP 5.6 as i am):
ension=php_pdo_sqlsrv_56_ts.dll
extension=php_sqlsrv_56_ts.dll

5. Microsoft® ODBC Driver 11 for SQL Server® ( this is for PHP 5.6 also )
You can download it from here: https://www.microsoft.com/en-us/download/details.aspx?id=36437
