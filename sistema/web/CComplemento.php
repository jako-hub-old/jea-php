<?php
/**
 * Esta clase es la base para cualquier complemento 
 * @package sistema.web
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jakop
 * 
 */
abstract class CComplemento {
    /**
     * Html generado
     * @var string 
     */
    protected $html;
    /**
     * Parametros para el html del complemento
     * @var array 
     */
    protected $_opcionesHtml = [];
    
    /**
     * Esta función debe ser implementada para poner en ella lógica 
     * que inicialice el complemento
     */
    public abstract function inicializar();
    
    /**
     * Esta función debe ser implementada para poner la logíca de 
     * ejecución del complemento
     */
    public abstract function iniciar();
    
    /**
     * Esta función es la que asigna los atributos a todo componente
     * <b>Nota</b>: Solo se asignarán valores a las variables cuyo nombre
     * empiece con _
     * @param array $atributos
     */
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
