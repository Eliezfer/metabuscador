<?php

require("recurso.php");

$keyword ="";

if (isset($_REQUEST["keyword"])) {
    $keyword = $_REQUEST["keyword"];
}

//endpoints
$url_redisw="http://redisw.uady.mx/api/busqueda-movil";
$url_agora = "http://www.agora.uady.mx/recurso/ajax/buscar";

//header de la consulta redisw
$http_header= array(
    'Content-Type' => 'application/x-www-form-urlencoded',
);

//body de la consulta redis 
$body_redisw= array(
    'consulta' => $keyword
);

//body de la consulta agora
$body_agora= array(
    'cadena' => $keyword
);

//hacer consulta
$response_redisw= request($url_redisw, $http_header, $body_redisw);
$recursos_redi= $response_redisw->response->docs;
$response_agora = request($url_agora, $http_header, $body_agora);

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


if(isset($_POST['filtro'])){
    $recursos = array();
    $filtro = $_POST['filtro'];
    foreach($recursos_redi as $recurso){
        $doc = json_decode(json_encode($recurso), true);
        $fecha = array_pop($doc['dc.date']);
        $recurso_obj = new recurso($doc['dc.title'][0], $fecha, $doc['author'], $doc['dc.identifier.uri']);
        array_push($recursos, $recurso_obj);
    }
    foreach($response_agora as $doc){
        $recurso_obj = new recurso($doc->titulo, $doc->fecha_registro, $doc->nombre." ".$doc->apellido, $doc->ubicacion);
        array_push($recursos, $recurso_obj);
    }
    if ($filtro=="titulo") {
        usort($recursos, "cmpByTitle");
    }
    if ($filtro=="fecha") {
        usort($recursos, "cmpByDate");
    }
    imprimirRecursos($recursos, count($recursos_redi), count($response_agora), $filtro);
}
else{
    ?>
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
}

function request($url, $http_header, $body)
{
    $jsonObj = null;
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $http_header,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $body 
    ));
    if( !$result = curl_exec($ch) ){
        print($ch);
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else{
        $jsonObj = json_decode($result);
    }
    curl_close($ch);
    return $jsonObj;
}

function cmpByTitle($a, $b) {
    $at = iconv('UTF-8', 'ASCII//TRANSLIT', $a->titulo);
    $bt = iconv('UTF-8', 'ASCII//TRANSLIT', $b->titulo);
    return strcmp($at,$bt);
}

function cmpByDate($a, $b) {
    return strcmp($a->fecha,$b->fecha);
}

function imprimirRecursos($recursos, $numRecurRedi,  $numRecurAgora, $filtro){
    ?>
    <body class="login">
        <div class="contenedor-formulario">
            <h1>Artículos encontrados por <?php  echo $filtro; ?>  </h1>
            <h1><span>REDIS: <?php  echo $numRecurRedi; ?> <span> </h1>
            <h1><span>AGORA: <?php  echo $numRecurAgora; ?> <span> </h1>
            <div class="listado-pendientes">
                <ol>
                <?php 
                    foreach ($recursos as $r) {
                ?> 
                    <li >
                        <?php  echo $r->titulo?>
                        <strong><?php echo "   ".$r->fecha;  ?></strong>
                    </li>
                    <?php
                    }
                    ?>
                </ol>
            </div>
        </div>
    </body>
    <?php
}