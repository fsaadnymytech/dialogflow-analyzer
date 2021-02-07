<?php
class Counter{
	#Esta clase sólo se usa para escribir el conteo de los análisis hechos:
	public function addOneUnit(){
		try {
		    #Primero se abre y se obtiene el contenido actual:
			$content = "";
			$fp = fopen("./bots_zips/counter_file.txt", "r");
			while (!feof($fp)){
			    $content .= fgets($fp);
			}
			fclose($fp);
			$content = trim($content, "\n");

			#Si es un número el contenido actual, se suma 1, y se sobreescribe el archivo:
			$new_content = "";
			if(is_numeric($content)) {
	        	$new_content = $content+1;
	        	$fp  = fopen("./bots_zips/counter_file.txt", "w");
				fwrite($fp , $new_content);
				fclose($fp);
	    	}
		} catch (Exception $e) {}
	}
}