<?php
class IntentResponseReducer{

	public function reduceIntentsResponse($intents_array){
		$reduced_intents_array = array();
		#Re recorre cada intent:
		foreach($intents_array as $intent){
			$aux_intent_array = array();
			$aux_intent_array["intent_name"] = $intent["intent_name"];
			#Re recorre cada respuesta:
			foreach($intent["responses"] as $response){
				if(array_key_exists("response_platform", $response)){
					#Si ya existe el canal de la actual respuesta, la informacion se agrega a la llave 
					#existente
					if(array_key_exists($response["response_platform"],$aux_intent_array)){
						$aux_intent_array[$response["response_platform"]] = 
								$aux_intent_array[$response["response_platform"]]." - ".
								$response["response_type"].": ".$response["response_content"];
					}
					#Si no existe, se crea
					else{
						$aux_intent_array[$response["response_platform"]] = 
								$response["response_type"].": ".$response["response_content"];
					}
				}
			}
			array_push($reduced_intents_array, $aux_intent_array);
		}
		return $reduced_intents_array;
	}
}