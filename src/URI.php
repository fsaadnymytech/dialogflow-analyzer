<?php
/*Esta clase URI representa las URIs que se definan para el proyecto*/
class Uri{
    /*Se dfinen los atributos de esta clase:*/
    public $definedUri; //Contiene el string de la URI. Ej: '/rsultados/vistas'
    public $method; //Método por el cual se pretende acceder a la URI
    public $functionToExecute; //Funcion que se debe ejecutar al tratar de acceder
    
    public function __construct($definedUri, $method, $function2Exec){
        $this->definedUri = $definedUri;
        $this->method = $method;
        $this->functionToExecute = $function2Exec;
    }
    
    /*El siguiente método se utiliza para verificar si una URI enviada
    coincide con la URI registrada, y si también coincide el método
    con el cual se trata de acceder. SI coincide, devuelve true.*/
    public function verifyMatch($sendUri, $sendMethod){
        $path = preg_replace('/:([\w]+)/','([^/]+)',$this->definedUri);
        $regex = '#^'.$path.'$#';
        if(preg_match($regex,$sendUri) && 
           $this->method == $sendMethod){
          return true;
        }else{
            return false;
        }
    }
    
    /*EL método anterior sólo verifica la URI enviada. El siguiente,
    la ejecuta:*/
    public function executeUriFunction($sendUri){
        //Se obtienen los parámetros QUE NO SON ENVIADOS POR LA URL:
        $requestParams = $_REQUEST;
        //Se obtienen los parámetros enviados por la URI:
        $path = preg_replace('/:([\w]+)/','([^/]+)',$this->definedUri);
        $regex = "#^".$path."$#";
        preg_match($regex,$sendUri,$uriParams);
        array_shift($uriParams);
        //Se ejecuta la funcion de la URI y se obtiene el resultado
        $response = 
            call_user_func_array($this->functionToExecute,$uriParams);
        //La respuesta puede ser una de dos cosas: Un string o un Objeto.
        if(is_string($response)){
            //Se retorna directamente la respuesta:
            echo $response;
        }else if(is_object($response) || is_array($response)){
            /*Si es JSON o array, se codifica como archivo JSON*/
            header("Content-type:application-json");
            echo json_encode($response);
        }
    }
}