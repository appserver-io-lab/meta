@ECHO OFF
ECHO Starting PHP FastCGI...

set php=php\php-cgi.exe
set ini=php\php.ini

%php% -c %ini% -b ${php-fpm.host}:${php-fpm.port}