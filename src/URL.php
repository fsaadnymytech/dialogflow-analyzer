<?php
/*Esta clase sólo se utiliza en las vistas, y se usa para identificar la 
ubicación del script en las rutas del proyecto*/
class URL{
    
    //Este método lo que hace es obtener la ruta completa del directorio actual
    public static function getCurrentFolder(){
        if(isset($_SERVER["HTTPS"])){
            $protocol = "https";
        }else{
            $protocol = "http";
        }
                
        $host = $_SERVER['HTTP_HOST'];
        
        $folder = 
            str_replace(basename($_SERVER['SCRIPT_NAME']),"",
                       $_SERVER['SCRIPT_NAME']);
        
        $urlCurrentFolder = $protocol."://".$host.$folder;
        
        $urlCurrentFolder = trim($urlCurrentFolder, "/");
        
        return $urlCurrentFolder;
    }
    
    public static function getResourceFromHere($urlResource){
        $urlResource = trim($urlResource,"/");
        $urlCurrentFolder = URL::getCurrentFolder();
        $urlResource = $urlCurrentFolder."/".$urlResource;
        return $urlResource;
    }
    
    public static function getCompleteURI(){
        if(isset($_SERVER["HTTPS"])){
            $protocol = "https";
        }else{
            $protocol = "http";
        }
        
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER["REQUEST_URI"];
        $completeUri = $protocol."://".$host.$uri;
        return $completeUri;
    }
}