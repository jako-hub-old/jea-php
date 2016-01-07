<?php

abstract class CComplemento {
    protected $html;
    protected $opcionesHtml = [];
    
    public abstract function inicializar();
    
    public abstract function iniciar();
    
    public function asignarAtributos($atributos = []){
        foreach ($atributos AS $nombre=>$valor) {
            if(property_exists($this, "_$nombre")){
                $this->{"_$nombre"} = $valor;
            }
        }
    }
    
    /**
     * Esta función permite cargar un complemento
     * @param string $ruta
     * @return CComplemento
     * @throws CExAplicacion
     */
    public static function cargarComplemento($ruta){
        # nombre del complemento que se intenta cargar
        $nombre = substr($ruta, strrpos($ruta, '.') + 1);
        # validamos si existe el complemento
        if(!file_exists(Sistema::resolverRuta($ruta, true))){
            throw new CExAplicacion("No existe el complemento '$nombre'");
        }
        Sistema::importar($ruta);
        $instancia = new $nombre();
        if(!$instancia instanceof CComplemento){
            throw new CExAplicacion("El complemento que trata de cargar no es valido '$nombre'");
        }
        return $instancia;
    }
}
