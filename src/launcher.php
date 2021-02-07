<?php
//Primero se importa el Autoloader:
require "./src/Autoloader.php";
Autoloader::registerAutoloadFunction();

//Muchos análisis pueden tardar varios minutos, se envía el tiempo límite de ejecución a 10 minutos:
set_time_limit(600);

//Se establece el manejador de errores:
function myErrorHandler($errno, $errstr, $errfile, $errline) {
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("myErrorHandler");

//Se obtienen las rutas definidas:
require "./routes/web.php";

//En este punto, ya están definidas las rutas, ahora se procesa 
//la peticion del cliente:
Route::submit();


