<?php

class Recurso  
{

    public function __construct($titulo, $fecha, $autor, $url,$descripcion, $bolsa) {
        $this->titulo = empty($titulo)?"Sin titulo":$titulo;
        $this->fecha = $fecha;
        $this->autor = $autor;
        $this->url = $url;
        $this->descripcion = $descripcion;
        $this->bolsa = $bolsa;
        $this->llenarBolsaTF();
    }

    public function llenarBolsaTF()
    {
        foreach($this->bolsa as $palabra => $value){
            $palabraEstandar = $this->quitar_tildes($palabra);
            $tituloEstandar = $this->quitar_tildes($this->titulo);
            $descrEstandar = $this->quitar_tildes($this->descripcion);
            $ocurreTitulo = substr_count($tituloEstandar, $palabraEstandar);
            $ocurreDescrip = substr_count($descrEstandar, $palabraEstandar);
            $this->bolsa[$palabra] = $ocurreDescrip+$ocurreTitulo;
        }
    }

    public function generarBolsaTF_IDF($bolsaIDF)
    {
        $this->bolsaTFIDF = array();
        $this->similitud = 0;
        foreach ($bolsaIDF as $termino) {
            $palabra = $termino->nombre;
            $frecuencia = $this->bolsa[$palabra];
            $this->bolsaTFIDF[$palabra] = $frecuencia * $termino->IDF;
            $this->similitud +=$this->bolsaTFIDF[$palabra];
        }
    }

    function quitar_tildes($cadena) {
        $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
        $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
        $texto = strtolower(str_replace($no_permitidas, $permitidas ,$cadena));
        return $texto;
    }
    

}
