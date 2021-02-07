<?php
class BotStructureVerifier{
	public function verify($extractedBotRoute){
		$return_code = 0;
		#Primero, se verifica que la carpeta de extracción contiene sólo tenga archivos JSON:
		$result_first_folder = $this->verifyFolderContent($extractedBotRoute);
		if($result_first_folder == 0){
			$extractedBotRoute = trim($extractedBotRoute, "/");
			#Luego, se verifica la existencia y contenido de la carpeta "intents":
			$intents_folder = $extractedBotRoute."/intents";
			$result_intents_folder = $this->verifyFolderContent($intents_folder);
			if($result_intents_folder != 0){
				if($result_intents_folder == 1){
					#No se encontró la carpeta de intents:
					$return_code = 2;
				}elseif($result_intents_folder == 2){
					$return_code = 1;
				}
			}
			#Luego, se verifica la existencia y contenido de la carpeta "entities":
			$entities_folder = $extractedBotRoute."/entities";
			$result_entities_folder = $this->verifyFolderContent($entities_folder);
			if($result_entities_folder != 0){
				if($result_entities_folder == 1){
					#No se encontró la carpeta de entidades:
					$return_code = 3;
					$return_code = $entities_folder;
				}elseif($result_entities_folder == 2){
					$return_code = 1;
				}
			}
		}elseif($result_first_folder == 1){
			#Si la ruta del bot extraído, no existe, significa que el error es desconocido (
			#porque debe existir)
			$return_code = 99;
		}
		elseif($result_first_folder == 2){
			$return_code = 1;
		}
		#Si no hubo ningun problema, se devuelve 0:
		return $return_code;
	}

	#Esta función se usa para verificar que los archivos de una carpeta sean solo JSON:
	private function verifyFolderContent($folder_path){
		$folder_path = trim($folder_path,"/");
		$folder_path = $folder_path."/";
		#Primero, se comprueba que lo que se haya pasado, sea la ruta de un folder:
		if(is_dir($folder_path)){
			#Se obtiene la lista de archivos en ese folder:
			$elements_in_folder = scandir($folder_path);
			#Se remueve la referencia a la carpeta actual (.) y anterior(..):
			$elements_in_folder = $this->removeFolderReferences($elements_in_folder);
			#Ahora, se verifican uno por uno los elementos:
			foreach($elements_in_folder as $element_in_folder){
				$file_complete_name = $folder_path.$element_in_folder;
				if(is_file($file_complete_name)){
					#Si es archivo, se verifica que sea de tipo JSON. Si no es JSON, se devuelve FALSE:
					if(pathinfo($file_complete_name, PATHINFO_EXTENSION) != "json"){
						#Código 2: Hay al menos un archivo no-JSON:
						return 2;
					}
				}
			}
		}else{
			#Código 1: No es un directorio.
			return 1;
		}
		#Código 0: No se detectó ningún error:
		return 0;
	}

	private function removeFolderReferences($dirElementsList){
		if(array_search(".", $dirElementsList) !== FALSE){
			#Se borra el elemento:
			unset($dirElementsList[array_search(".", $dirElementsList)]);
		}
		if(array_search("..", $dirElementsList) !== FALSE){
			#Se borra el elemento:
			unset($dirElementsList[array_search("..", $dirElementsList)]);
		}
		$dirElementsList = array_values($dirElementsList);
		return $dirElementsList;
	}
}