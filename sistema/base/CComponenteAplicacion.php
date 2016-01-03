<?php
/**
 * Esta clase es la base de todo componente que use la clase aplicación, si un componente
 * no es instancia de esta clase no será aceptado como componente
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.1
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
            throw new Exception("La propiedad a la que trata de acceder no está definida");
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
            $this->$nombre = $valor;
        }
    }
    
    /**
     * Esta función puede ser usada para inicializar valores antes de iniciar la aplicación
     */
    public function antesDeIniciar(){}
    
    /**
     * Todo componente de aplicación deberá tener una función que no inicie
     */
    public abstract function iniciar();    
    
}
