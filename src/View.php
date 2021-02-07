<?php
class View{
    private $variables; //Este es un vector o JSON que almacena variables que se quieran mostrar en la vista.
    private $viewFile; //Este es un string con el nombre del archivo de vista (php, html)
    private $renderedFile; //Este método guarda el String del archivo de vista renderizado.
    
    //Este método lo que hace es devolver el String del archivo de vista
    //renderizado
    public function getRenderedView($viewFile, $variables = null){
        if(isset($variables) && 
           (is_array($variables) || is_object($variables))){
            $this->variables = $variables;
        }
        $this->viewFile = $viewFile;
        //Se obtiene la ruta de la ubicación del archivo:
        $file = "./app/views/".$viewFile;
        
        //---------------------
        ob_start();
        if(file_exists($file)){
            include $file;
            $renderedFile = ob_get_contents();
        }else{
           $renderedFile = "El archivo no existe..." ;
        }
        ob_end_clean();
        //----------------------
        
        return $renderedFile;
    }
}