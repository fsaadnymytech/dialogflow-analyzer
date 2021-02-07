<!DOCTYPE html>
<html>
    <head>
        <title>Analizador de Dialogflow</title>
        <link rel="icon" href="<?php echo URL::getResourceFromHere("img/favicon.ico");?>" 
        type="image/x-icon" />
        <link rel="stylesheet" href="<?php echo URL::getResourceFromHere("css/index.css");?>">    
    </head>
    
    <body>
        <div class="container_1">
            <div class="main-container">
            <h1>Analizador de Dialogflow</h1>
            <form method="POST" action="<?php echo 
            URL::getResourceFromHere("generate_file");?>" enctype="multipart/form-data">
                <div>
                    <p>¿No sabes cómo obtener el ZIP de tu agente virtual? 
                        <a href="<?php echo URL::getResourceFromHere("docs_get_zip");?>" target="_blank">Haz clic aquí</a></p>
                    <label>Selecciona el ZIP de tu agente virtual:  </label>
                    <input type="file" name="zip_file" required="required" class="input-button">
                    <br><br>
                </div>
                <div>
                    <label>Selecciona la información que deseas obtener: </label>
                    <div class="options-container">
                        <div class="subtitle-options">Problemas de entrenamiento:</div>
                        <div class="option-class">
                            <input type="radio" id="conflic_usersays_1" name="confict_type" value="1" required="required">
                            <label for="conflic_usersays_1">Usersays repetidos en intents diferentes.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="repeated_synonyms" name="confict_type" value="2" required="required">
                            <label for="repeated_synonyms">Sinónimos repetidos en varias entidades.</label>
                        </div>
                        <div class="subtitle-options">Información general sobre el agente:</div>
                        <div class="option-class">
                            <input type="radio" id="only_1_usersay" name="confict_type" value="3" required="required">
                            <label for="only_1_usersay">Intents con un único usersay.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="all_usersays_and_state" name="confict_type" value="5" required="required">
                            <label for="all_usersays_and_state">Todos los usersays con sus intents, contextos y estados.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="intents_responses" name="confict_type" value="6" required="required">
                            <label for="intents_responses">Todas las respuestas de todos los intents.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="intents_contexts" name="confict_type" value="11" required="required">
                            <label for="intents_contexts">Contextos de entrada y salida de todos los intents.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="intents_with_action" name="confict_type" value="8" required="required">
                            <label for="intents_with_action">Intents con actions.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="only_1_synonym_entity" name="confict_type" value="4" required="required">
                            <label for="only_1_synonym_entity">Entidades con un único sinónimo.</label>
                        </div>
                        <div class="option-class">
                            <input type="radio" id="synonyms_entities" name="confict_type" value="7" required="required">
                            <label for="synonyms_entities">Todas las entidades con todos sus sinónimos.</label>
                        </div>                        
                        <div style="display: inline-block; border: 1px solid #ABADAD; padding:10px">
                            <input type="radio" id="search_entities_in_usersays" name="confict_type" value="9" required="required">
                            <label for="search_entities_in_usersays">Buscar entidades en usersays.</label>
                            <br> 
                            <label for="entities_input">Nombres de las entidades separadas por coma:
                            </label>
                            <input type="text" name="entities_list_to_search" placeholder="Ej: laptop,smartphone,tv">
                        </div>
                        <div class="option-class">
                            <input type="radio" id="all_entities_in_usersays" name="confict_type" value="10" required="required">
                            <label for="all_entities_in_usersays">Todas las entidades presentes en todos los usersays.</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="button-class">Generar archivo</button>
            </form>
            </div>
        </div>
        <footer>
            fsaad.nymytech@gmail.com - Enero de 2021
        </footer>
    </body>
</html>