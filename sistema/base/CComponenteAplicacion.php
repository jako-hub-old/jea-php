<?php
/**
 * Esta clase es la base de todo componente que use la clase aplicación, si un componente
 * no es instancia de esta clase no será aceptado como componente
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.3
 * @copyright (c) 2015, jakop
 */

abstract class CComponenteAplicacion {
    /**
     * Todo componente debe tener un nombre, ID almacena el nombre
     * del componente
     * @var string 
     */
    protected $ID;
    /**
     * Esta variable se puede usar para asignarle un identificador especial
     * para las variables que serán seteadas en el componente, si está vacio
     * quiere decir que todas las variables del componente podrán ser seteadas
     * al llamar la función asignarAtributos
     * 
     * @var string 
     */
    protected $c = '';
    
    /**
     * Esta es la función magica para get, solo retornará atributos existentes
     * en la clase, de lo contrario retornará null
     * 
     * @param string $nombre
     * @return mixed
     */
    public function __get($nombre) {
        return property_exists($this, $nombre)? $this->$nombre : null;
    }
    
    /**
     * Esta es la función magica para set, solo permitirá setear atributos que estén
     * definidos en un componente de lo contrario arrojará una excepción
     * 
     * @param string $nombre
     * @param mixed $valor
     * @throws Exception  si no se encuentra definida la propiedad
     */
    public function __set($nombre, $valor){
        if(!isset($this->$nombre)){
            throw new Exception("La propiedad a la que trata de acceder ('$nombre') no está definida");
        }
        $this->$nombre = $valor;
    }
    
    /**
     * Esta función permite asigar a un componente todas sus variables definidas
     * desde el archivo de configuraciones
     * @param array $atributos
     */
    public function asignarAtributos(array $atributos = []){
        foreach ($atributos AS $nombre=>$valor){
            $this->{$this->c.$nombre} = $valor;
        }
    }
    
    /**
     * Esta función puede ser usada para inicializar valores antes de iniciar la aplicación
     */
    public function antesDeIniciar(){}
    
    /**
     * Todo componente podrá sobreescribir la función de iniciar
     */
    public function iniciar(){}
            
    /**
     * Esta función retorna el id del componente
     * @return string
     */
    public function getID(){
        return $this->ID;
    }
    
    /**
     * Esta función permite cargar un componente, de inmediato lo inicializa
     * @param array $configs
     * @return CComponenteAplicacion
     * @throws CExAplicacion
     */
    public static function cargarComponente($configs){
        $ruta = $configs['ruta'];
        $clase = $configs['clase'];
        #eliminamos estas dos pociciones, el resto son configuraciones
        unset($configs['ruta'], $configs['clase']);
        $archivo = Sistema::resolverRuta("$ruta.$clase", true);
        if(!file_exists($archivo)){
            throw new CExAplicacion("No se encuentra el componente $clase ");
        }
        Sistema::importar($archivo, false);
        $componente = new $clase();
        $componente->asignarAtributos($configs);
        $componente->antesDeIniciar();
        $componente->iniciar();
        return $componente;
    }
}
