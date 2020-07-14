<?php
require("recurso.php");
require("termino.php");

function cmpByTitle($a, $b) {
    $at = iconv('UTF-8', 'ASCII//TRANSLIT', $a->titulo);
    $bt = iconv('UTF-8', 'ASCII//TRANSLIT', $b->titulo);
    return strcmp($at,$bt);
}

function cmpByDate($a, $b) {
    return strcmp($b->fecha,$a->fecha);
}

function cmbByFrecuencia($a, $b)
{
    
    return $a->similitud < $b->similitud;
}

function generarBolsaDePalabras($keyword)
{
    $bolsa = array();
    $palabras = explode(" ",$keyword);
    foreach ($palabras as $palabra) {
        $bolsa[$palabra] = 0;
    }
    return $bolsa;
}

function generarBolsadIDF($bolsa, $recursos){
    $bolsaIDF = array();
    $N = sizeof($recursos);
    foreach ($bolsa as $palabra => $value) {
        $DF=0;
        foreach ($recursos as $recurso) {
            if($recurso->bolsa[$palabra]>0){
                $DF++;
            };
        }
        $IDF =log10($N/$DF)+1;
        $termino = new termino($palabra,$DF,$IDF);
        array_push($bolsaIDF, $termino);
    }
    return $bolsaIDF;
}

function estandarizarRecursos($recursos_redi, $recursos_agora, $bolsa)
{
    $recursos = array();
    foreach($recursos_redi as $recurso){
        $doc = json_decode(json_encode($recurso), true);
        $titulo = $doc['dc.title'][0];
        $fecha = array_pop($doc['dc.date']);
        $autor = $doc['author'];
        $url = $doc['dc.identifier.uri'];
        $decripcion = array_key_exists('dc.description.abstract',$doc)?
                        $doc['dc.description.abstract'][0]:"sin descripcion";
        $recurso_obj = new recurso($titulo, $fecha, $autor, $url ,$decripcion, $bolsa);
        array_push($recursos, $recurso_obj);
    }
    foreach($recursos_agora as $doc){
        $titulo = $doc->titulo;
        $fecha = $doc->fecha_registro;
        $autor = $doc->nombre." ".$doc->apellido;
        $url = $doc->ubicacion;
        $decripcion = $doc->descripcion;
        $recurso_obj = new recurso($titulo, $fecha, $autor, $url, $decripcion, $bolsa);
        array_push($recursos, $recurso_obj);
    }
    return $recursos;
}

function imprimirRecursos($recursos, $numRecurRedi,  $numRecurAgora)
{
?>

    <body class="login">
        
        <div class="contenedor-formulario">
            <h1>Artículos encontrados</h1>
            <h1><span>REDIS: <?php echo $numRecurRedi; ?> <span> </h1>
            <h1><span>AGORA: <?php echo $numRecurAgora; ?> <span> </h1>
            <div class="listado-pendientes">
                <table id="domainsTable" class="tablesorter">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Fecha</th>
                            <th>TF-IDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                            <?php
                            foreach ($recursos as $r) {
                            ?>
                                <tr>
                                    <td><?php echo $r->titulo ?></td>
                                   <td> <strong><?php echo $r->fecha;  ?></strong></td>
                                   <td> <strong><?php echo $r->similitud;  ?></strong></td>
                                
                            <?php
                            }
                            ?>
                        
                    </tbody>

                </table>
            </div>
        </div>
        <script>
    $(document).ready(function() 
        { 
            $("#domainsTable").tablesorter({sortList: [[3,1],[2,0]]}); 
        } 
    );
    </script>
    </body>
<?php
}