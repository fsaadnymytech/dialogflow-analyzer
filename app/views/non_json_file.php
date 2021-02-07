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
				<h3>Error</h3>
				<p>Dentro de la carpeta comprimida se ha encontrado un archivo con una extensión no válida. </p>
				<a href="<?php echo URL::getResourceFromHere("/");?>">Regresar</a>
				<h4>¿Qué puedo hacer?</h4>
				<p>Este error se da porque se ha encontrado un archivo con extensión diferente a JSON. Puedes hacer lo siguiente:</p>
				<ol>
					<li>Descomprimir el archivo ZIP del agente.</li>
					<li>Buscar los archivos cuya extensión sea diferente a <i>.json</i> y eliminarlos.</li>
					<li>Volver a comprimir la carpeta del agente con extensión .ZIP</li>
					<li>Probar de nuevo con ese archivo coomprimido.</li>
				</ol>
			</div>
		<div>
		<footer>
            fsaad.nymytech@gmail.com - Enero de 2021
        </footer>
	</body>
</html>