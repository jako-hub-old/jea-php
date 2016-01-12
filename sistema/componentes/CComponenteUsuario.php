<?php
/**
 * Esta clase es la plantilla para genrar los componentes de usuario
 * contiene las funciones básicas, todo componente debe implmentar 
 * la función autenticar para poner la validación del inicio de sesión
 * @package sistema.componentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jakop
 * @property string $usuario nombre de usuario
 * @property string $ID Id del usuario logueado
 * @property array $atributos atributos registrados en el componente
 * @property array $esVisitante true si el usuario no está logueado, false si lo está
 */
abstract class CComponenteUsuario extends CComponenteAplicacion {
    /**
     * Si hubo o no error al autenticarse
     * @var boolean 
     */
    protected $error = false;
    /**
     * Nombre de usuario contra el cual se validará
     * @var string 
     */
    protected $usuario;
    /**
     * Contraseña contra la que se hará la validación
     * @var string 
     */
    protected $clave;
    /**
     * ID del usuario que inicia sesión
     * @var string 
     */
    protected $_ID;
    /**
     * Bandera que permite saber si un usuario ha iniciado sesión
     * el valor true significa que es invitado (no ha iniciado sesión)
     * el valor false indica que se inició correctamente sesión
     * @var boolean 
     */
    private $esVisitante = true;
    /**
     * El componente de usuario puede tener atributos o variables distintas 
     * a las declaradas en la clase, a estas se les conoce como atributos
     * @var array 
     */
    private $atributos = [];
    /**
     * Nombre del componente en la sesión
     * @var string¨
     */
    private $_identificador = "usr_com";
    
    public function __construct($usuario = null, $clave = null) {
        $this->usuario = $usuario;
        $this->clave = $clave;
        
        # definimos un caracter para las variables que queremos se seteen 
        # desde la configuración del componente
        $this->c = "_";
    }
    
    /**
     * Esta función es llamada por la aplicación para iniciar el componente
     * por ellos aprovechamos e implementamos esta la logica de iniciar 
     * los valores del componente
     */
    public function antesDeIniciar() {
        if(Sistema::apl()->mSesion->existeAtributo($this->_identificador)){
            $sesion = Sistema::apl()->mSesion->getAtributo($this->_identificador);
            $this->esVisitante = false;
            $this->usuario = $sesion['usuario'];
            $this->ID = $sesion['id'];
            $this->atributos = isset($sesion['atributos'])? $sesion['atributos'] : [];
        }
    }
    
    /**
     * Sobreescribimos la función __get para que solo retorne los atributos
     * que empiezan con guión bajo
     * @param string $nombre
     * @return mixed
     * @throws CExAplicacion
     */
    public function __get($nombre) {
        if(key_exists($nombre, $this->atributos)){
            return $this->atributos[$nombre];
        } else if(property_exists($this, "_$nombre")){
            return $this->{"_$nombre"};
        } else if(method_exists($this, "get" . ucfirst($nombre))){
            return $this->{"get" . ucfirst($nombre)}();
        } else {
            throw new CExAplicacion("La propiedad $nombre no está definida en ".__CLASS__);
        }
    }
    
    /**
     * Esta función debe ser sobreescrita para agregar la lógica que permita iniciar
     * sesión
     */
    public  function autenticar(){
        return $this->error;
    }
    
    /**
     * Esta función agrega los atributos del componente a la variable de sesión
     * @param string $id
     * @param string $usuario
     */
    public function iniciarSesion($id, $usuario){
        # no queremos iniciar sesión si ya está iniciada
        if(!$this->esVisitante){ return; }
        
        Sistema::apl()->mSesion->setAtributo($this->_identificador, [
            'id' => $id,
            'usuario' => $usuario,
        ]);
        $this->esVisitante = false;
    }
    
    /**
     * Esta función asigna los atributos a la sesión del componente
     */
    private function ponerAtributosEnSesion(){
        # si no hemos iniciado sesión no modificamos nada
        if($this->esVisitante){ return; }
        
        Sistema::apl()->mSesion->setAtributo($this->_identificador, [
            'id' => $this->id,
            'usuario' => $this->usuario,
            'atributos' => $this->atributos,
        ]);
    }
    
    /**
     * Esta función permite asignar un nuevo atributo al componente en la sesión
     * @param string $nombre
     * @param mixed $valor
     */
    public function setAtributo($nombre, $valor = ""){
        # si no se ha iniciado sesión no queremos setear atributos
        if($this->esVisitante){ return; }
        
        $this->atributos[$nombre] = $valor;
        $this->ponerAtributosEnSesion();
    }
    
    /**
     * Esta función permite verificar si un atributo existe en el componente
     * @param string $nombre
     * @return boolean
     */
    public function getAtributo($nombre){
        return key_exists($nombre, $this->atributos)?
                $this->atributos[$nombre] : null;
    }
    
    /**
     * Esta función permite remover un atributo del componente
     * @param string $nombre
     */
    public function removerAtributo($nombre){
        # no queremos tocar nada si no se ha iniciado sesión
        if($this->esVisitante){ return; }
        
        unset($this->atributos[$nombre]);
        $this->ponerAtributosEnSesion();
    }
    
    /**
     * Esta función permite remover el componente de la sesión
     */
    public function cerrarSesion(){
        # no queremos cerrar sesión si nisiquiera se ha iniciado
        if($this->esVisitante){ return; }
        
        Sistema::apl()->mSesion->borrarAtributo($this->_identificador);
        $this->esVisitante = true;
    }
    
    /**
     * Esta función retorna true si no se ha iniciado sesión (es visitante) o false si ya inició sesión
     * @return boolean
     */
    public function getEsVisitante(){
        return $this->esVisitante;
    }
    
    /**
     * Esta función retorna los atributos registrados en el componente
     * @return array
     */
    public function getAtributos(){
        return $this->atributos;
    }
    
    public function getUsuario(){
        return $this->usuario;
    }    
}