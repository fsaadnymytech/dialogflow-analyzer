<?php
class StaticResource{
  public function getStaticResource($resourceFile){
        //---------------------
        ob_start();
        if(file_exists($resourceFile)){
            readfile($resourceFile);
            #include $resourceFile;
            $file = ob_get_contents();
        }
        ob_end_clean();
        //----------------------
        return $file;
    }
}