<?php
class IntentResponse{

	public $response_json;

	function __construct($response_json){
		$this->response_json = $response_json;
	}

	public function getFullResponse(){
		#Una respuesta sólo pertenece a una plataforma:
		if(array_key_exists("platform", $this->response_json)){
			$platform_name = $this->response_json["platform"];
		}else{
		    $platform_name = "Plataforma por defecto";
		}
		#Puede ser, de uno de los siguientes tipos de respuesta:
		$response_array = array();
		#1. Tipo 0: Respuestas sólo de texto: Puede tener una lista de textos que se devuelven de 
		#forma aleatoria:
		$response_content = "";
		$response_type = "";
		$response_order = 0;
		if($this->response_json["type"] == "0"){
			$response_type = "Respuesta de texto";
			$response_order = 1;
			if (array_key_exists("speech", $this->response_json)) {
				$response_content = $response_content. "{";
		        #Puede haber mas de un texto de respuesta:
		        if(is_array($this->response_json["speech"])){
		        	$i=1;
			        foreach($this->response_json["speech"] as $response_text){
			        	$response_content = $response_content.
			            " Posibilidad #".$i.": [".$response_text."], ";
			            $i = $i + 1;
			        }
		        }
		        #O una respuesta única:
		        else{
		        	$response_content = $response_content."Respuesta única: [".
		        	$this->response_json["speech"]."]";
		        }
		        
		        $response_content = trim($response_content, ", ");
		        $response_content = $response_content."}";
		    }
		    $response_array["response_platform"] = $platform_name;
		    $response_array["response_order"] = $response_order;
		    $response_array["response_type"] = $response_type;
		    $response_array["response_content"] = $response_content;
		}
		#2.  Tipo 1: Respuesta de tipo menú:
		elseif($this->response_json["type"] == "1"){
			$response_type = "Respuesta menú";
			$response_order = 2;
			$response_content = $response_content."{";
			if(array_key_exists("title", $this->response_json)){
				$response_content = $response_content."Titulo: [".$this->response_json["title"]."] ";
			}
			if(array_key_exists("subtitle", $this->response_json)){
				$response_content = 
					$response_content."Subtítulo: [".$this->response_json["subtitle"]."] ";
			}
			if(array_key_exists("buttons", $this->response_json)){
				$response_content = $response_content."Botones: [";
				foreach($this->response_json["buttons"] as $button){
					$response_content = $response_content.$button["text"].", ";
				}
				$response_content = trim($response_content,", ");
				$response_content = $response_content."]";
			}
			$response_content = $response_content."}";
		    $response_array["response_platform"] = $platform_name;
		    $response_array["response_order"] = $response_order;
		    $response_array["response_type"] = $response_type;
		    $response_array["response_content"] = $response_content;
		}
		#3.  Tipo 2: Respuestas rapidas:
		elseif($this->response_json["type"] == "2"){
			$response_type = "Respuesta rápida";
			$response_order = 4;
			$response_content = $response_content."{";
			if(array_key_exists("title", $this->response_json)){
				$response_content = $response_content."Titulo: [".$this->response_json["title"]."] ";
			}
			if(array_key_exists("subtitle", $this->response_json)){
				$response_content = 
					$response_content."Subtítulo: [".$this->response_json["subtitle"]."] ";
			}
			if(array_key_exists("replies", $this->response_json)){
				$response_content = $response_content."Botones: [";
				foreach($this->response_json["replies"] as $replies_text){
					$response_content = $replies_text.", ";
				}
				$response_content = trim($response_content,", ");
				$response_content = $response_content."]";
			}
			$response_content = $response_content."}";
		    $response_array["response_platform"] = $platform_name;
		    $response_array["response_order"] = $response_order;
		    $response_array["response_type"] = $response_type;
		    $response_array["response_content"] = $response_content;
		}
		#4.  Tipo 3: Imagen:
		elseif($this->response_json["type"] == "3"){
			$response_type = "Imagen";
			$response_order = 3;
			$response_content = $response_content."{";
			if(array_key_exists("title", $this->response_json)){
				$response_content = $response_content."Titulo: [".$this->response_json["title"]."] ";
			}
			if(array_key_exists("imageUrl", $this->response_json)){
				$response_content = 
					$response_content."URL Imagen: [".$this->response_json["imageUrl"]."] ";
			}
			$response_content = $response_content."}";
		    $response_array["response_platform"] = $platform_name;
		    $response_array["response_order"] = $response_order;
		    $response_array["response_type"] = $response_type;
		    $response_array["response_content"] = $response_content;
		}
		#5.  Tipo 4: Payload personalizado:
		elseif($this->response_json["type"] == "4"){
			$response_type = "Payload personalizado";
			$response_order = 5;
			#Como los casos de payload personalizados pueden variar, no se puede generar
			#una estructura general. La siguiente es la usada por algunas organizaciones:
			if(array_key_exists("payload", $this->response_json)){
				$response_content=$this->getCustomizedPayload1($this->response_json["payload"]);
			}else{
				$response_content = $response_content."{NO FUE POSIBLE OBTENER RESPUESTA}";
			}
		    $response_array["response_platform"] = $platform_name;
		    $response_array["response_order"] = $response_order;
		    $response_array["response_type"] = $response_type;
		    $response_array["response_content"] = $response_content;
		}
		return $response_array;
	}

	private function getCustomizedPayload1($customized_payload){
		$response_content = "{";
		#Un payload puede contener texto:
		if(array_key_exists("text", $customized_payload)){
			$response_content = $response_content."Texto:[".$customized_payload["text"]."], ";
		}
		#Un dato
		if(array_key_exists("data", $customized_payload)){
			$response_content = $response_content."Dato:[".$customized_payload["data"]["value"]."], ";
		}
		#Un menu:
		if(array_key_exists("elements", $customized_payload)){
			foreach($customized_payload["elements"] as $menu_elements){
				$response_content = $response_content."Menú:[";
		        #Cada $response_list es una respuesta:
		        if(array_key_exists("title", $menu_elements)){
		        	$response_content = $response_content."Titulo:[".$menu_elements["title"]."], ";
		        }
		        if(array_key_exists("subtitle", $menu_elements)){
		            $response_content = $response_content."Subtitulo:[".$menu_elements["subtitle"]."], ";
		        }
		        if(array_key_exists("buttons", $menu_elements)){
		        	$response_content = $response_content."Botones:[";
		            foreach($menu_elements["buttons"] as $button){
		                if(array_key_exists("title", $button)){
		                    $response_content  = $response_content." ".$button["title"].", ";
		                }
		            }
		            $response_content = trim($response_content, ", ");
		            $response_content = $response_content."] ";
		        }
		        $response_content = $response_content."] ";
		    }
		}
		$response_content = trim($response_content, ", ");
		$response_content = $response_content."}";
		return $response_content;
	}
}