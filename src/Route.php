<?php
class Route{
    /*En este vector estático se guardan la URI que se definan*/
    public static $urisArray = array();
    
    /*Los siguientes métodos se usan para agregar las rutas_*/
    public static function get($definedUri, $uriFunction){
        self::add("GET",$definedUri, $uriFunction);
    }
    
    public static function post($definedUri, $uriFunction){
        self::add("POST",$definedUri, $uriFunction);
    }
    //NOTA: Por el momento no se necesita el resto de métodos (DELETE y PUT)
    
    public static function add($requestMethod, $definedUri, $uriFunction){
        //Antes de agregar la URI al vector, se limpia el string:
        /*las uris no deben quedar así: '/ruta/ruta/', sino así: 'ruta/ruta'*/
        $definedUri = trim($definedUri, "/");
        if(strlen($definedUri)==0){
            $definedUri = "/";
        }
        /*Ahora si, se agrega: */
        $uriObjectToAdd = new URI($definedUri, $requestMethod, $uriFunction);
        self::$urisArray[] = $uriObjectToAdd;
    }
    
    //La siguiente funcion ya recibe la petición del cliente, vertifica si coincide con alguna URI registrada, y si coincide, la ejecuta:
    public static function submit(){
        //Se obtiene el método por el cual el cliente está tratando de acceder:
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        //Se ubtiene la URI ingresada por el cliente:
        if(!isset($_GET["uri"])){
            $requestURI = "/";
        }else{
            $requestURI = $_GET["uri"];
        }
        //La URI ingresada por el cliente es probable que este sucia, se limpia:
        $requestURI = trim($requestURI, "/");
        if(strlen($requestURI)==0){
            $requestURI = "/";
        }

        /*Se recorren todas las URIs registradas para ver si lo que el cliente
        está tratando de acceder, realmente existe:*/
        foreach(Route::$urisArray as $savedUri){
            if($savedUri->verifyMatch($requestURI,$requestMethod)){
                $savedUri -> executeUriFunction($requestURI);
                //Finaliza la ejecución:
                return;
            }
        }
        
        /*Si no coincide con ninguna URI, se muestra un mensaje por defecto:*/
        header("Content-type:text/html");
        echo ((new View())->getRenderedView("not_found.php")); 
    }
        
        
}