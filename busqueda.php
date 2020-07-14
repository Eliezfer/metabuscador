<?php

require("coneccion.php");
require("filtro.php");

$keyword ="";

if (isset($_REQUEST["keyword"])) {
    $keyword = $_REQUEST["keyword"];
}

//endpoints
$url_redisw="http://redisw.uady.mx/api/busqueda-movil";
$url_agora = "http://www.agora.uady.mx/recurso/ajax/buscar";

//body de la consulta redis 
$body_redisw= array('consulta' => $keyword);

//body de la consulta agora
$body_agora= array('cadena' => $keyword);



//hacer consulta
$response_redisw= request($url_redisw, $body_redisw);
$recursos_redi= $response_redisw->response->docs;
$response_agora = request($url_agora, $body_agora);

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Buscador</title>
        <link rel="stylesheet" href="css/busqueda.css">
        <link rel="stylesheet" href="css/lista.css">
    </head>
<?php
$bolsa = generarBolsaDePalabras($keyword);
$recursos =  estandarizarRecursos($recursos_redi, $response_agora, $bolsa);


if(isset($_POST['filtro'])){
    $filtro = $_POST['filtro'];
    if ($filtro=="titulo") {
        usort($recursos, "cmpByTitle");
    }
    if ($filtro=="fecha") {
        usort($recursos, "cmpByDate");
    }
    imprimirRecursos($recursos, count($recursos_redi), count($response_agora), $filtro);
}
else{
    $bolsaIDF = generarBolsadIDF($bolsa, $recursos);
    foreach ($recursos as $rec) {
        $rec->generarBolsaTF_IDF($bolsaIDF);
    }
    
    usort($recursos, "cmbByFrecuencia");

    imprimirRecursos($recursos, count($recursos_redi), count($response_agora), 'frecuencia');
}

/* ?>
        <body class="login">
            <div class="contenedor-formulario">
                <h1>Artículos encontrados en REDISW</h1>
                <div class="listado-pendientes">
                    <ol>
                    <?php 
                        foreach ($response_redisw->response->docs as $doc) {
                    ?> 
                        <li >
                            <?php  echo $doc->title[0]; ?>
                        </li>
                    
                        <?php
                        }
                        ?>
                    </ol>
                </div>
            </div>
            
            <div class="contenedor-formulario">
                <h1>Artículos encontrados en AGORA</h1>
                <div class="listado-pendientes">
                
                    <ol>
                    <?php 
                    if (!$response_agora) {
                        echo "No se encontraron resultados";
                    }
                        foreach ($response_agora as $doc) {
                    ?> 
                        <li >
                            <?php  echo $doc->titulo; ?>
                        </li>
                    
                        <?php
                        }
                        ?>
                    </ol>
                </div>
            </div>

        </body>

    <?php
}*/
