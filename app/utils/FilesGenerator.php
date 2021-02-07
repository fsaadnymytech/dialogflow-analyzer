<?php
class FilesGenerator{
    public $ZIP_FILES_FOLDER = "bots_zips/zip_files/";
    public $EXTRACTED_ZIP_FILES_FOLDER = "bots_zips/unziped_files/";
    
    public function generateFile(){
        #1.Se valida si existen las carpetas:  "bots_zips/zip_files/" y "bots_zips/unziped_files/".
        #Si no existen, se crean:
        $this->verifyFolderExistence($this->ZIP_FILES_FOLDER );
        $this->verifyFolderExistence($this->EXTRACTED_ZIP_FILES_FOLDER);
        #2. Se valida que el archivo cargado sea correcto:
        $verification_result = $this->validateSentFile();
        if($verification_result != 0){
            if($verification_result == 1){
                return ((new View())->getRenderedView("filetype_error.php"));
            }elseif($verification_result == 2){
                return ((new View())->getRenderedView("filesize_error.php"));
            }
        }
        #3. Se genera un nombre unico para el nuevo archivo:
        $file_name = $this->generateUniqueName();
        #4. Se guarda el bot en una carpeta y se extrae:
        $this->saveAndUnzip($file_name);
        #5. Como ya esta extraido, se borra el archivo ZIP:
        unlink($this->ZIP_FILES_FOLDER.$file_name);
        #6. Se verifica que el contenido extraído tenga la estructura de un bot:
        $structure_verification = $this->verifyBotStructure($file_name);
        if($structure_verification !=0){
            return ((new View())->getRenderedView("error_in_folder.php"));
        }

        #7. Una vez verificado, se obtiene el tipo de información:
        $botAnalizer = new BotAnalizer();
        return 
        $botAnalizer ->  execAnalysis
        ($this->EXTRACTED_ZIP_FILES_FOLDER.$file_name."/", $_POST["confict_type"]);
    }
    
    private function  generateUniqueName(){
        $aux_date = new DateTime();
        $id_timestamp = $aux_date -> getTimestamp();
        $id_random = rand(1, 100);
        $zip_name = $id_timestamp."_".$id_random."_".$_FILES['zip_file']['name'];
        #El nombre tiene la estruuctura:
        #<timestamp>_<un numero aleatorio>_<nombre del zip>
        return $zip_name;
    }
    
    private function saveAndUnzip($zip_name){
        //Se guarda el archivo ZIP del bot en la ruta especifica:
        opendir($this->ZIP_FILES_FOLDER);
        copy($_FILES['zip_file']['tmp_name'], $this->ZIP_FILES_FOLDER.$zip_name);
        //Se crea una carpeta sólo para ese bot:
        mkdir($this->EXTRACTED_ZIP_FILES_FOLDER.$zip_name, 0777);
        //Y se extrae en ella:
        $zip = new ZipArchive;
        $existe = $zip -> open($this->ZIP_FILES_FOLDER.$zip_name);
        if($existe === TRUE){
            $zip -> extractTo($this->EXTRACTED_ZIP_FILES_FOLDER.$zip_name."/");
            $zip -> close();
        }else{
            return "Error";
        }
    }

    private function validateSentFile(){
        #1. Se valida el tipo de archivo enviado, debe ser un archivo tipo ZIP:
        if(mime_content_type ($_FILES['zip_file']['tmp_name']) != "application/zip"){
            #Se retorna 1, si el archivo cargado NO es un ZIP.
            return 1;
        }
        #2. Se valida el peso del archivo, no debe superar XMB
        if(filesize($_FILES['zip_file']['tmp_name']) > 
            #6291456){
            100000000000){
            #Se retorna 2, si el archivo cargado supera los XMB:
            return 2;
        }
        #Se retorna 0 si no se detectó ningún error:
        return 0;
    }

    private function verifyBotStructure($zip_name){
        $botStructureVerifier = new BotStructureVerifier();
        return $botStructureVerifier -> verify($this->EXTRACTED_ZIP_FILES_FOLDER.$zip_name);
    }

    private function verifyFolderExistence($folder_route){
        if(!is_dir($folder_route)){
            mkdir($folder_route, 0777);
        }
    }
}
