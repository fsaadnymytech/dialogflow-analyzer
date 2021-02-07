<?php
class BotAnalizer{


	public function execAnalysis($folderRoute, $type){
		#Antes que nada, se agrega un elemento al contador de analisis:
		(new Counter())->addOneUnit();
		try{
			//1. Se verifica qué operación se desea hacer:
			if($type == "1"){
				//Si el código es 1, quiere decir que se desea obtener la lista de los usersays
				//repetidos cuyo contexto de entrada sea el mismo:
				$this->getConflictRepeatedUsersays($folderRoute);
			}
			elseif($type == "2"){
				//Si el código es 2, quiere decir que se desea obtener la lista de los sinonimos
				//que se encuentran en varias entidades:
				$this->getSynonymsInEntities($folderRoute);
			}
			elseif($type == "3"){
				//Si el código es 3, quiere decir que se desea obtener la lista de los intents con
				//un sólo usersay:
				$this->getUniqueUsersayIntents($folderRoute);
			}
			elseif($type == "4"){
				//Si el código es 4, quiere decir que se desea obtener la lista de las entidades con un
				//solo sinónimo:
				$this->getUniqueSynonymsEntities($folderRoute);
			}
			elseif($type == "5"){
				//Si el código es 5, quiere decir que se desea obtener todos los usersays junto con su estado, intents y contexto:
				$this->getAllUsersaysInformation($folderRoute);
			}
			elseif($type == "6"){
				//Si el código es 6, quiere decir que se desean obtener las respuestas de los intents:
				$this->getIntentsResponses($folderRoute);
			}
			elseif($type == "7"){
				//Si el código es 7, quiere decir que se desean obtener todos los sinónimos de 
				//todas las entidades_
				$this->getAllEntitiesSynonyms($folderRoute);
			}
			elseif($type == "8"){
				//Si el código es 8, quiere decir que se desean obtener todos los intents
				//con un action_
				$this->getIntentsWithActions($folderRoute);
			}
			elseif($type == "9"){
				//Si el código es 9, quiere decir que se desean obtener los usersays donde 
				//se encuentra una lista de entidades
				$this->getListEntitiesUsersays($folderRoute);
			}
			elseif($type == "10"){
				//Si el código es 10, quiere decir que se desean obtener todos los usersays
				//con todas sus entidades
				$this->getEntitiesInAllUsersays($folderRoute);
			}
			elseif($type == "11"){
				//Si el código es 11, quiere decir que se desean obtener todos los intents, 
				//junto con sus contextos de entrada y salida
				$this->getContextsIntents($folderRoute);
			}else{
				//Si el código no fue ninguno de los anteriores, se devuelve un error:
				return ((new View())->getRenderedView("option_error.php"));
			}
		}catch(Exception $e){
			return ((new View())->getRenderedView("unknown_error.php"));
		}
	}

	#Este método devuelve un vector de objetos con la información de los intents:
	private function getIntentsInformation($folderRoute, $intents_filenames){
		$intents_array = array();
		foreach ($intents_filenames as $file){
		    $intent_name = "";
		    $intent_status = 0;
		    $intent_contexts = array();
		        
		    $intent= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    
		    $intent_name = $intent["name"];
		    
		    if($intent["priority"] > -1){
		        $intent_status = 1;
		    }else{
		        $intent_status = 0;
		    }
		    
		    if(array_key_exists("contexts",$intent)){
		        foreach($intent["contexts"] as $intent_context){
		            array_push($intent_contexts, $intent_context);
		        }
		    }
		    
		    array_push($intents_array, array("intent"=>$intent_name,
		                                    "status"=>$intent_status,
		                                    "input_contexts"=>$intent_contexts));
		}
		return $intents_array;
	}

	#Este método devuelve un vector de objetos con información de los usersays:
	private function getUsersaysInformation($folderRoute, $usersays_filenames){
		$usersays_array = array();
		foreach($usersays_filenames as $file){
		    $intent = "";
		    $usersay = "";
		    
		    $usersays_list= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    
		    foreach($usersays_list as $usersay){
		        if(array_key_exists("data",$usersay)){
		            //Parabras que componen el usersay:
		            $aux_complete_usersay = "";
		            foreach($usersay["data"] as $phrase){
		                $aux_complete_usersay = $aux_complete_usersay." ".$phrase['text'];
		            }
		            $intent = basename($file, "_usersays_es.json");
		            array_push($usersays_array, array("intent"=>$intent, 
		                                              "usersay"=>$aux_complete_usersay));
		        }
		    }
		}
		return $usersays_array;
	}

	public function getConflictRepeatedUsersays($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de usersays
		$usersays_filenames = array();
		#En este vector se guardan los nombres de archivos de metadatos de los intents
		$intents_filenames = array(); 
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') !== FALSE){
		        array_push($usersays_filenames, $file);
		    }elseif($file != "." && $file !=".."){
		        array_push($intents_filenames, $file);
		    }
		}
		#Se obtiene la información de los metadatos de todos los intents:
		$intents_array = $this->getIntentsInformation($folderRoute, $intents_filenames);
		#Se obtiene la información de los usersays:
		$usersays_array = $this->getUsersaysInformation($folderRoute, $usersays_filenames);
		#Ahora, a cada objeto de usersay, se le va a agregar la información de su intent:
		$just_intent_names = array_column($intents_array, "intent");;
		for ($i=0; $i<count($usersays_array); $i++){
		    $intent_key = array_search($usersays_array[$i]["intent"], $just_intent_names);
		    $usersays_array[$i]["status"] =$intents_array[$intent_key]["status"];
		    $usersays_array[$i]["input_contexts"] =$intents_array[$intent_key]["input_contexts"];
		}
		#Se quitan todos los usersays de los intents inactivos:
		$usersays_array = array_filter($usersays_array,
		                              function($value, $key){
		                                  return $value["status"] == 1;
		                              },
		                               ARRAY_FILTER_USE_BOTH
		                             );
		$usersays_array = array_values($usersays_array);
		#Se eliminan los usersays repetidos en el mismo intent: 
		$usersays_array = array_unique($usersays_array, SORT_REGULAR);
		$usersays_array = array_values($usersays_array);

		#Se obtiene la lista de sólo los textos de los usersays:
		$just_usersays_array = array_column($usersays_array, "usersay");
		#Se obtiene el conteo de cada texto de usersay:
		$usersays_count = array_count_values($just_usersays_array);
		#Se filtran por aquellos que tengan mas de uno:
		$usersays_count = array_filter($usersays_count,
		                              function($value, $key){
		                                  return $value > 1;
		                              },
		                               ARRAY_FILTER_USE_BOTH
		                             );
		#Se obtienen los textos repetidos (las llaves de $usersays_count son los textos)
		$repeated_usersay_strings = array_keys($usersays_count);

		#Se busca la ubicación de esos strings en el vector de objetos:
		#NOTA: $usersays_array y $just_usersays_array tienen las mismas llaves.
		$keys_to_search = array();#En este vector se van a guardar las llaves de los objetos
		#Se recorre cada frase repetida:
		foreach($repeated_usersay_strings as $repeated_usersay_string){
		    $aux_keys_array = array_keys($just_usersays_array,$repeated_usersay_string);
		    $keys_to_search = array_merge($keys_to_search, $aux_keys_array);
		}

		#Se guardan todos los objetos de los usersays repetidos:
		$repeated_usersays_array = array();
		foreach($keys_to_search as $key_to_search){
		    array_push($repeated_usersays_array, $usersays_array[$key_to_search]);
		}

		#En este momento, $repeated_usersays_array contiene todos los objetos de 
		#los usersays que están repetidos, pero ahora, se deben obtener aquellos
		#que tengan el mismo contexto de entrada.

		#IMPORTANTE:Antes de continuar, se debe borrar de nuevo los duplicados:
		#PORQUE PUEDEN HABER:
		$repeated_usersays_array = array_unique($repeated_usersays_array,SORT_REGULAR);
		#Se obtienen dos vectores de vectores asociativos: Uno que contiene 
		#usersay context e intent, y otro que contiene sólo usersay y context
		$usersays_and_contexts = array(); #Este array guarda usersay y contexto.
		$usersays_contexts_intents = array(); #Este array guarda los 3 campos
		foreach($repeated_usersays_array as $usersays_obj){
		    #Lo que se necesita recorrer es el vector de cotextos:
		    if(count($usersays_obj["input_contexts"]) > 0){
		        foreach($usersays_obj["input_contexts"] as $context_object){
		            #--Vector 3 campos:
		            array_push($usersays_contexts_intents, 
		                      array("usersay"=>$usersays_obj["usersay"],
		                           "context"=>$context_object,
		                           "intent" => $usersays_obj["intent"])
		                      );
		            #--Vector 2 campos:
		            array_push($usersays_and_contexts, 
		                      array("usersay"=>$usersays_obj["usersay"],
		                           "context"=>$context_object)
		                      );
		        }
		    }else{
		        #--Vector 3 campos:
		        array_push($usersays_contexts_intents, 
		                      array("usersay"=>$usersays_obj["usersay"],
		                           "context"=>"NA",
		                           "intent" => $usersays_obj["intent"])
		                      );
		        #--Vector 2 campos:
		        array_push($usersays_and_contexts, 
		                      array("usersay"=>$usersays_obj["usersay"],
		                           "context"=>"NA")
		                      );
		    }
		}

		
		#Se buscan los objetos del par usersay-context que están repetidos:
		#En el siguiente vector se van a guardar los indices de las posiciones de los objetos
		#con casos repetidos:
		$positions_to_search = array();
		for($i=0; $i<count($usersays_and_contexts); $i++){
		    $positions_list = array_keys($usersays_and_contexts, $usersays_and_contexts[$i]);
		    if(count($positions_list)>1){
		       $positions_to_search = array_merge($positions_to_search, $positions_list);
		    }
		}

		$positions_to_search = array_unique($positions_to_search);
		$positions_to_search = array_values($positions_to_search);

		#Ahora, se buscan esas posiciones y se guardan los vectores en el siguiente vector:
		$final_repeated_usersays = array();
		foreach($positions_to_search as $i){
		    array_push($final_repeated_usersays, 
		               array(
		                    "usersay" => $usersays_contexts_intents[$i]["usersay"],
		                   "context" =>  $usersays_contexts_intents[$i]["context"]
		               ));
		}
		$final_repeated_usersays = array_unique($final_repeated_usersays, SORT_REGULAR);
		$final_repeated_usersays = array_values($final_repeated_usersays);

		#Y se buscan los intents:
		for($i = 0; $i <count($final_repeated_usersays); $i++ ){ 
		    $repeated_intents_string = "";
		    $aux_usersay = $final_repeated_usersays[$i]["usersay"];
		    $aux_context = $final_repeated_usersays[$i]["context"];
		    for($j=0; $j<count($usersays_contexts_intents); $j++){
		        if(
		            $aux_usersay 
		            == 
		           $usersays_contexts_intents[$j]["usersay"] 
		           &&
		           $aux_context ==
		           $usersays_contexts_intents[$j]["context"]
		          ){
		            $repeated_intents_string = $repeated_intents_string.
		                $usersays_contexts_intents[$j]["intent"].", ";
		        }
		    } 
		    $repeated_intents_string = trim($repeated_intents_string, ", ");
		    $final_repeated_usersays[$i]["intents"]=$repeated_intents_string;
		}
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel del vector $final_repeated_usersays
		$columnsNamesArray = array("Usersays", 
									"Contexto de entrada de los intents",
									 "Intents donde se encuentran");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($final_repeated_usersays, 
			$columnsNamesArray,"Reporte_usersays_en_conflicto.xlsx");
	}

	public function getSynonymsInEntities($folderRoute){
		$all_entities_files = scandir($folderRoute."/entities/");
		//Se obtienen sólo los archivos que contienen los sinonimos de entidades:
		$entries_files = array_filter($all_entities_files,
		                              function($value, $key){
		                                  return preg_match("/_entries_/i", $value);
		                              },
		                               ARRAY_FILTER_USE_BOTH
		                             );
		//Se crea un vector asociativo con los sinonimos de cada entidad:
		$synonyms_array = array();
		//NOTA: Cada archivo, es un "tipo de entidad":
		foreach($entries_files as $file_name){
			$entry_json_file = json_decode(
		        file_get_contents($folderRoute."/entities/".$file_name),
		        $assoc = TRUE);
		    //Valor entidad
		    foreach($entry_json_file as $entity_value){
		        //Sinonimos de entidad
		        foreach($entity_value["synonyms"] as $synonym){
		            array_push($synonyms_array, 
		                       array("entity_type" => basename($file_name,"_entries_es.json"),
		                             "entity_value" => $entity_value["value"],
		                             "synonym" =>$synonym));
		        }
		    }
		}
		#Pueden haber casos donde esté repetido en la misma entidad. Esto no es bueno, pero
		#tampoco es malo. Se quitan esos casos:
		$synonyms_array = array_unique($synonyms_array, SORT_REGULAR);
		$synonyms_array = array_values($synonyms_array);

		#Se obtienen los sinónimos repetidos:
		$repeated_synonyms = array();
		$just_synonyms_array = array_column($synonyms_array, "synonym");
		foreach($synonyms_array as $value){
			#Si hay más de una ubicación para el sinonimo actual, se agrega a la lista de
			#repetidos
		    $keys_list = array_keys($just_synonyms_array, $value["synonym"]);
		    if(count($keys_list)>1){
		    	for($i=0; $i<count($keys_list); $i++){
		    		array_push($repeated_synonyms, $synonyms_array[$keys_list[$i]]);
		    	}
		    }
		}
		#El vector '$repeated_synonyms' tiene registros duplicados, se eliminan:
		$repeated_synonyms = array_unique($repeated_synonyms, SORT_REGULAR);
		$repeated_synonyms = array_values($repeated_synonyms);

		#Se ordenan los registros de entidades:
		$just_entity_type_column = array_column($repeated_synonyms, "entity_type");
		$just_entity_value_column = array_column($repeated_synonyms, "entity_value");
		$just_synonym_column = array_column($repeated_synonyms, "synonym");
		array_multisort($just_synonym_column, SORT_DESC, 
        		        $just_entity_type_column, SORT_DESC, 
                		$just_entity_value_column, SORT_DESC, 
                		$repeated_synonyms);
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel del vector '$repeated_synonyms'
		$columnsNamesArray = array("Tipo entidad", 
									"Valor entidad",
									 "Sinónimo");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($repeated_synonyms, 
			$columnsNamesArray,"Reporte_sinonimos_en_varias_entidades.xlsx");

	}

	public function getUniqueUsersayIntents($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de usersays
		$usersays_filenames = array();
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') !== FALSE){
		        array_push($usersays_filenames, $file);
		    }
		}

		#Ahora, se obtienen los intents:
		$intents_and_unique_usersay = array();
		foreach($usersays_filenames as $file_name){
		    $usersays_json_list = json_decode(
		        file_get_contents($folderRoute."/intents/".$file_name),
		        $assoc = TRUE);
		    //Solo intents con 1 frase:
		    if(count($usersays_json_list)=== 1){
		    	$aux_complete_usersay = "";
		    	if(array_key_exists("data",$usersays_json_list[0])){
		            //Parabras que componen el usersay:
		            foreach($usersays_json_list[0]["data"] as $phrase){
		                $aux_complete_usersay = $aux_complete_usersay." ".$phrase['text'];
		            }
		        }
		        array_push($intents_and_unique_usersay, 
		        	array("intent"=>basename($file_name, "_usersays_es.json"),
		        		"usersay"=>$aux_complete_usersay));
		    }
		}
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel del vector '$repeated_synonyms'
		$columnsNamesArray = array("Intent", 
									"Usersay");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($intents_and_unique_usersay, 
			$columnsNamesArray,"Reporte_intents_con_1_solo_usersay.xlsx");
	}

	public function getUniqueSynonymsEntities($folderRoute){
		$all_entities_files = scandir($folderRoute."/entities/");
		//Se obtienen sólo los archivos que contienen los sinonimos de entidades:
		$entries_files = array_filter($all_entities_files,
		                              function($value, $key){
		                                  return preg_match("/_entries_/i", $value);
		                              },
		                               ARRAY_FILTER_USE_BOTH
		                             );
		//Se crea un vector asociativo con los sinonimos de cada entidad:
		$synonyms_array = array();
		//NOTA: Cada archivo, es un "tipo de entidad":
		foreach($entries_files as $file_name){
			$entry_json_file = json_decode(
		        file_get_contents($folderRoute."/entities/".$file_name),
		        $assoc = TRUE);
		    //Valor entidad
		    foreach($entry_json_file as $entity_value){
		        //Sinonimos de entidad
		        foreach($entity_value["synonyms"] as $synonym){
		            array_push($synonyms_array, 
		                       array("entity_type" => basename($file_name,"_entries_es.json"),
		                             "entity_value" => $entity_value["value"],
		                             "synonym" =>$synonym));
		        }
		    }
		}
		#Pueden haber casos donde esté repetido en la misma entidad. Esto no es bueno, pero
		#tampoco es malo. Se quitan esos casos:
		$synonyms_array = array_unique($synonyms_array, SORT_REGULAR);
		$synonyms_array = array_values($synonyms_array);

		//Cada registro es un sinonimo, vamos a obtener cuantos sinonimos tiene cada valor de entidad:
		$just_entity_value_array = array_column($synonyms_array, "entity_value");
		$entity_synonyms_count = array_count_values($just_entity_value_array);
		//Ahora, filtramos las entidades con solo 1 sinonimo:
		$entity_synonyms_count = array_filter($entity_synonyms_count,
		                              function($value, $key){
		                                  return $value == 1;
		                              },
		                               ARRAY_FILTER_USE_BOTH
		                             );
		$entity_values_1_synonym = array_keys($entity_synonyms_count);

		#Se obtienen los índices de los registros de los valores entidad:
		$index_to_search = array();
		foreach($entity_values_1_synonym as $entity){
		   $index_to_search= array_merge($index_to_search, 
		                array_keys($just_entity_value_array, $entity));    
		}

		$entity_values_synonyms = array();
		foreach($index_to_search as $i){
		    array_push($entity_values_synonyms,$synonyms_array[$i]);
		}

		#Se ordenan los registros de entidades:
		$just_entity_type_column = array_column($entity_values_synonyms, "entity_type");
		array_multisort($just_entity_type_column, SORT_ASC,
                		$entity_values_synonyms);
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel del vector '$repeated_synonyms'
		$columnsNamesArray = array("Tipo entidad", 
									"Valor entidad",
									 "Sinónimo");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($entity_values_synonyms, 
			$columnsNamesArray,"Reporte_entidades_con_1_sinonimo.xlsx");

	}

	public function getAllUsersaysInformation ($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de usersays
		$usersays_filenames = array();
		#En este vector se guardan los nombres de archivos de metadatos de los intents
		$intents_filenames = array(); 
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') !== FALSE){
		        array_push($usersays_filenames, $file);
		    }elseif($file != "." && $file !=".."){
		        array_push($intents_filenames, $file);
		    }
		}
		#Se obtiene la información de los metadatos de todos los intents:
		$intents_array = $this->getIntentsInformation($folderRoute, $intents_filenames);
		#Se obtiene la información de los usersays:
		$usersays_array = $this->getUsersaysInformation($folderRoute, $usersays_filenames);
		#Ahora, a cada objeto de usersay, se le va a agregar la información de su intent:
		$just_intent_names = array_column($intents_array, "intent");;
		for ($i=0; $i<count($usersays_array); $i++){
		    $intent_key = array_search($usersays_array[$i]["intent"], $just_intent_names);
		    if($intents_array[$intent_key]["status"] == 1){
		    	$aux_status = "INTENT ACTIVO";
		    }else{
		    	$aux_status = "INTENT INACTIVO";
		    }
		    $usersays_array[$i]["status"] = $aux_status;
		    $usersays_array[$i]["input_contexts"] =
		    	implode(", ",$intents_array[$intent_key]["input_contexts"]);

		}	
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel:
		$columnsNamesArray = array("Intent", 
									"Usersay",
									 "Estado",
									 "Contextos de entrada");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($usersays_array, 
			$columnsNamesArray,"Reporte_todos_los_usersays.xlsx");
	}

	public function getIntentsResponses ($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de metadatos de los intents
		$intents_files_names = array();
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') == FALSE && $file !== "." && $file !== ".."){
		        array_push($intents_files_names, $file);
		    }    
		}
		#Se recorren los archivos para obtener la respuesta de cada canal:
		$intents_array = array();
		foreach ($intents_files_names as $file){	        
		    $intent_file_content= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    $intent_name = $intent_file_content["name"];
		    #En el siguiente vector, se va a guardar cada respuesta del presente intent:
		    $responses_list = array();
		    #Algunos intents no tienen respuestas. Se verifica si tienen:
		    if(array_key_exists("responses", $intent_file_content)){
		    	$responses = $intent_file_content["responses"][0]["messages"];
		    	#Primero, se va a obtener una lista con el par: "plataforma", "respuesta"
		    	foreach($responses as $response){
		    		$intentResponse = new IntentResponse($response);
		    		array_push($responses_list, $intentResponse->getFullResponse());
		    	}
		    }
		    array_push($intents_array, array(
		    								"intent_name"=>$intent_name,
		    								"responses"=>$responses_list
		    								));
		}
		#--------
		#Ya se tienen todas las respuestas. Ahora, se deben obtener todas las plataformas, 
		#lo que sucede es que algunos intents no tienen respuesta para todas las plataformas:
		$just_responses_arrays = array_column($intents_array, "responses");

		#Se obtienen todos los nombres de las plataformas:
		$all_platform_names = array();
		foreach($just_responses_arrays as $aux_intent_responses){
			foreach($aux_intent_responses as $aux_platform_response){
				if(array_key_exists("response_platform", $aux_platform_response)){
					if($aux_platform_response["response_platform"] != "Plataforma por defecto"){
						array_push($all_platform_names, $aux_platform_response["response_platform"]);
					}
				}				
			}
		}

		#Se eliminan repetidos.
		$all_platform_names = array_unique($all_platform_names);

		#Se organizan por orden alfabético
		sort($all_platform_names);
		$all_platform_names = array_values($all_platform_names);

		#Se agrega como primer elemento, la plataforma por defecto:
		array_unshift($all_platform_names, "Plataforma por defecto");

		#En este momento, el vector $intents_array puede tener divididas las respuestas
		#de cada canal en varias respuestas. Se reduce a un sólo texto por canal:
		$reduced_intents_array = (new IntentResponseReducer())->
							reduceIntentsResponse($intents_array);
		
		#Ahora, se guardan en un nuevo vector:
		$sorted_intents_array = array();
		foreach($reduced_intents_array as $intent_information){
			#Se crea el vector asociativo del intent:
			$aux_intent_array = array();
			#Se agrega el nombre del intent
			$aux_intent_array["intent_name"] = $intent_information["intent_name"];
			#Se agrega la respuesta por cada canal (aun aquellos canales vacíos):
			for($i=0; $i<count($all_platform_names); $i++){
				#Si la plataforma existe como llave, se agrega su contenido al nuevo vector:
				if(array_key_exists($all_platform_names[$i], $intent_information)){
					$aux_intent_array[$all_platform_names[$i]] = 
						$intent_information[$all_platform_names[$i]];
				}else{
				#Si no, se deja vacío su contenido
					$aux_intent_array[$all_platform_names[$i]] = "NA";
				}
			}
			array_push($sorted_intents_array, $aux_intent_array);
		}
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel:
		$columnsNamesArray = $all_platform_names;
		array_unshift($columnsNamesArray, "Intent");		
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($sorted_intents_array, 
			$columnsNamesArray,"Reporte_respuestas_de_intents.xlsx");
	}


	public function getAllEntitiesSynonyms($folderRoute){
		$all_entities_files = scandir($folderRoute."/entities/");
		//Se obtienen sólo los archivos que contienen los sinonimos de entidades:
		$entries_files = array_filter($all_entities_files,
		                              function($value, $key){
		                                  return preg_match("/_entries_/i", $value);
		                              },
		                               ARRAY_FILTER_USE_BOTH
		                             );
		//Se crea un vector asociativo para cada valor entidad:
		$entities_array = array();
		//NOTA: Cada archivo, es un "tipo de entidad":
		foreach($entries_files as $file_name){
			$entry_json_file = json_decode(
		        file_get_contents($folderRoute."/entities/".$file_name),
		        $assoc = TRUE);
		    //Valor entidad
		    foreach($entry_json_file as $entity_value){
		    	array_push($entities_array, 
		                   array("entity_type" => basename($file_name,"_entries_es.json"),
		                    "entity_value" => $entity_value["value"],
		                    "synonyms" =>implode(", ", $entity_value["synonyms"])));
		    }
		}
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Finalmente, se genera el archivo de Excel del vector '$repeated_synonyms'
		$columnsNamesArray = array("Tipo entidad", 
									"Valor entidad",
									 "Sinónimos");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($entities_array, 
			$columnsNamesArray,"Reporte_sinónimos_de_entidades.xlsx");

	}

	public function getIntentsWithActions($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de metadatos de los intents
		$intents_files_names = array();
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') == FALSE && $file !== "." && $file !== ".."){
		        array_push($intents_files_names, $file);
		    }    
		}
		#Se recorren los archivos para encontrar su campo :
		$intents_array = array();
		foreach ($intents_files_names as $file){	        
		    $intent_file_content= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    $intent_name = $intent_file_content["name"];
    		if(array_key_exists("responses", $intent_file_content)){
    			foreach($intent_file_content["responses"] as $intent_response){
    				if(array_key_exists("action", $intent_response)){
    					if(strlen($intent_response["action"]) > 0){
	    					array_push($intents_array ,
	    						array("intent"=>$intent_name, "action"=>$intent_response["action"])
	    					);
    					}
		            }
    			}
    		}
    	}
    	#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
    	#Finalmente, se genera el archivo de Excel del vector '$repeated_synonyms'
		$columnsNamesArray = array("Intent", 
									"Action");
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($intents_array, 
			$columnsNamesArray,"Reporte_intents_con_actions.xlsx");
	}

	public function getListEntitiesUsersays($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de usersays
		$usersays_filenames = array();
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') !== FALSE){
		        array_push($usersays_filenames, $file);
		    }
		}

		#Se obtiene la lista de las entidades a buscar:
		$entities_to_search = $_POST["entities_list_to_search"];
		$entities_to_search = trim($entities_to_search);
		$entities_to_search = trim($entities_to_search,",");
		$entities_to_search = explode(",", $entities_to_search);
		$entities_to_search = array_unique($entities_to_search);
		$entities_to_search = array_values($entities_to_search);

		#Se recorre cada usersay:
		$usersays_array = array();
		foreach($usersays_filenames as $file){
		    $intent = "";
		    $usersay = "";
		    $usersays_list= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    foreach($usersays_list as $usersay){
		        if(array_key_exists("data",$usersay)){
		            //Parabras que componen el usersay:
		            $aux_complete_usersay = "";
		            $entities_list = array();
		            foreach($usersay["data"] as $phrase){
		                $aux_complete_usersay = $aux_complete_usersay." ".$phrase['text'];
		                #Se verifica si la ppalabra actual tiene un meta:
		                if(array_key_exists("meta", $phrase)){
		                	$entity_aux = trim($phrase["meta"],"@");
		                	array_push($entities_list, $entity_aux);
		                }
		            }
		            $intent = basename($file, "_usersays_es.json");
		            #Se revisa :
		            $exist_in_array = false;
		            $entities_list2 = array();
		            for($i=0; $i<count($entities_list); $i++){
		            	if(in_array($entities_list[$i], $entities_to_search)){
		            		$exist_in_array = true;
		            		array_push($entities_list2, $entities_list[$i]);
		            	}
		            }
		            if($exist_in_array){
		            	array_push($usersays_array, array("intent"=>$intent, 
		                                              "usersay"=>$aux_complete_usersay,
		                                          	  "entities"=>$entities_list2));
		            }
		        }
		    }
		}
		
		#Ahora, se crea un nuevo vector:
		$entities_and_usersays = array();
		foreach($entities_to_search as $entity_to_search){
			foreach($usersays_array as $usersay){
				#Si la entidad está en el presente usersay, se agrega al vector:
				if(in_array($entity_to_search, $usersay["entities"])){
					array_push($entities_and_usersays,
							array("entity" => $entity_to_search,
								  "intent" => $usersay["intent"],
								  "usersay" => $usersay["usersay"]
								));
				}
			}
		}
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
		#Se genera el archivo de Excel:
		$columnsNamesArray = array("Entidad", 
									"Intent",
									"Usersay"
								);
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($entities_and_usersays, 
			$columnsNamesArray,"Reporte_busqueda_de_entidades.xlsx");
	}

	public function getEntitiesInAllUsersays($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de usersays
		$usersays_filenames = array();
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') !== FALSE){
		        array_push($usersays_filenames, $file);
		    }
		}

		#Se recorre cada usersay:
		$usersays_array = array();
		foreach($usersays_filenames as $file){
		    $intent = "";
		    $usersay = "";
		    $usersays_list= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    foreach($usersays_list as $usersay){
		        if(array_key_exists("data",$usersay)){
		            //Parabras que componen el usersay:
		            $aux_complete_usersay = "";
		            $entities_list = "";
		            foreach($usersay["data"] as $phrase){
		                $aux_complete_usersay = $aux_complete_usersay." ".$phrase['text'];
		                #Se verifica si la palabra actual tiene un meta:
		                if(array_key_exists("meta", $phrase)){
		                	$entity_aux = trim($phrase["meta"],"@");
		                	$entities_list = $entities_list.", ".$entity_aux;
		                }
		            }
		            $entities_list = trim($entities_list, ", ");
		            $intent = basename($file, "_usersays_es.json");
		            array_push($usersays_array, array("intent"=>$intent, 
		                                              "usersay"=>$aux_complete_usersay,
		                                          	  "entities"=>$entities_list));
		            
		        }
		    }
		}
		#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);		
		#Se genera el archivo de Excel:
		$columnsNamesArray = array("intent", 
									"usersay",
									"entities"
								);
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($usersays_array, 
			$columnsNamesArray,"Reporte_entidades_en_usersays.xlsx");
	}

	public function getContextsIntents($folderRoute){
		$all_intents_files = scandir($folderRoute."/intents/");
		#En este vector se guardan los nombres de los archivos de metadatos de los intents
		$intents_files_names = array();
		foreach($all_intents_files as $file){
		    if(strpos($file, '_usersays_') == FALSE && $file !== "." && $file !== ".."){
		        array_push($intents_files_names, $file);
		    }    
		}
		#Se recorren los archivos para encontrar su campo :
		$intents_array = array();
		foreach ($intents_files_names as $file){	        
		    $intent_file_content= json_decode(
		        file_get_contents($folderRoute."/intents/".$file),
		        $assoc = TRUE);
		    $intent_name = $intent_file_content["name"];
		    $input_contexts = "";
		    $output_contexts = "";
		    $intent_state = "";
		    #Primero se buscan los contextos de entrada:
		    if(array_key_exists("contexts", $intent_file_content)){
		    	foreach($intent_file_content["contexts"] as $input_context){
		    		$input_contexts = $input_contexts.", ".$input_context;
		    	}
		    }
		    $input_contexts = trim($input_contexts, ", ");
		    #Ahora, los contextos de salida:
    		if(array_key_exists("responses", $intent_file_content)){
    			foreach($intent_file_content["responses"] as $intent_response){
    				if(array_key_exists("affectedContexts", $intent_response)){
    					foreach($intent_response["affectedContexts"] as $output_context){
    						$output_contexts = $output_contexts.", ".$output_context["name"];
    					}
    				}
    			}    				
    		}
    		$output_contexts = trim($output_contexts, ", ");
    		#El estado del intent:
    		if(array_key_exists("priority", $intent_file_content)){
    			if($intent_file_content["priority"] > -1){
    				$intent_state = "ACTIVO";
    			}else{
    				$intent_state = "INACTIVO";
    			}
    		}
    		array_push($intents_array,array("intent"=>$intent_name,
    										"input_contexts"=>$input_contexts,
    										"output_contexts"=>$output_contexts,
    										"intent_state"=>$intent_state
    									));
    	}
    	#Antes de generar el Excel, se borra el archivo ZIP extraído:
		$this->deleteFolder($folderRoute);
    	#Finalmente, se genera el archivo de Excel del vector '$repeated_synonyms'
		$columnsNamesArray = array("Intent", 
									"Contextos de entrada",
									"Contextos de salida",
									"Estado del intent"
								);
		$excelGenerator = new ExcelGenerator();
		$excelGenerator -> getExcelFile($intents_array, 
			$columnsNamesArray,"Reporte_contextos_de_intents.xlsx");
	}

	private function deleteFolder($folder){
        #Se limpia la ruta:
        $folder = trim($folder, "/");
        $folder = $folder."/";
        #Se obtienen todos los elementos del folder
        $elements_in_extracted_folder = scandir($folder);
        #Se quita la referencia de la carpeta actual y la carpeta anterior:
        if(array_search(".", $elements_in_extracted_folder) !== FALSE){
            unset($elements_in_extracted_folder[array_search(".", $elements_in_extracted_folder)]);
        }
        if(array_search("..", $elements_in_extracted_folder) !== FALSE){
            unset($elements_in_extracted_folder[array_search("..", $elements_in_extracted_folder)]);
        }
        $elements_in_extracted_folder = array_values($elements_in_extracted_folder);
        #Se recorre cada elemento del vector:
        foreach($elements_in_extracted_folder as $element){
            if(is_file($folder.$element)){
                //Si es un archivo, se elimina:
                unlink($folder.$element);
            }elseif(is_dir($folder.$element)){
            	//Si es una carpeta, se ejecuta este mismo metodo para borrarla:
            	$aux_element = trim($element, "/");
                $aux_element = $aux_element."/";
                $this->deleteFolder($folder.$aux_element);
            }
        }
        //En este punto, la carpeta ya debe estar vacía y se puede eliminar, pero, de todas
        //formas, se verifica:
        #NOTA: Una carpeta si está vacía, tiene 2 elementos: ".", ".."
        if(count(scandir($folder)) == 2){
            rmdir($folder);
        }
    }
}