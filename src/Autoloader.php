<?php
class Autoloader{
    public static function registerAutoloadFunction(){
        spl_autoload_register(array("Autoloader","load"), true, true);
    }
    
    public static function load($classToFind){
        $fileNameToSearch = $classToFind.".php";
        //Se establecen las carpetas donde el autoloader debe buscar
        //las clases:
        $autoloaderDirs = ["./app","./src"];
        foreach($autoloaderDirs as $directory){
            if(self::searchFile($directory, $fileNameToSearch)){
                return;
            }
        }
    }
    
    private static function searchFile($directory, $fileNameToSearch){
        //Se escanean todos los elementos dentro de la carpeta.
        $elmentsInDirectory = scandir($directory);
        foreach($elmentsInDirectory as $elmentInDirectory){
            $elementRoute = 
                realpath($directory.DIRECTORY_SEPARATOR.$elmentInDirectory);
            
            if(is_file($elementRoute)){
                if($fileNameToSearch == $elmentInDirectory){
                    require_once $elementRoute;
                    return true;
                }
            }else 
            if ($elmentInDirectory != "." && $elmentInDirectory != "..") {
                self::searchFile($elementRoute, $fileNameToSearch);
            }
        }
        return false;
    }
}