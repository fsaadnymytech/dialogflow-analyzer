<!DOCTYPE html>
<html>
	<head>
		<title>Error</title>
		<link rel="icon" href="<?php echo URL::getResourceFromHere("img/favicon.ico");?>" 
        type="image/x-icon" />
		<link rel="stylesheet" href="<?php echo URL::getResourceFromHere("css/docs.css");?>">  
		<meta charset="UTF-8">
	</head>
	<body>
		<div class="container_1">
			<div class="main-container">
				<h3>Error en el contenido del archivo ZIP</h3>
				<p>Se han encontrado problemas en el archivo cargado. Esto se puede dar por alguna de las siguientes razones:</p>
				<ol>
					<li>Después de extraer el contenido del archivo ZIP, se encontraron archivos con extensión diferente a JSON.</li>
					<li>Después de extraer el archivo ZIP, no se encontró la carpeta "intents"</li>
					<li>Después de extraer el archivo ZIP, no se encontró la carpeta "entities"</li>
					<li>Simplemente seleccionaste el archivo ZIP incorrecto o el archivo está dañado.</li>
				</ol>
				<h4>¿Qué puedo hacer?</h4>
				<p>La solución más efectiva, es descargar de nuevo el ZIP del agente y volver a ejecutar el proceso. </p>
				<a href="<?php echo URL::getResourceFromHere("/");?>">Regresar</a>
			</div>
		<div>
		<footer>
            fsaad.nymytech@gmail.com - Enero de 2021
        </footer>
	</body>
</html>