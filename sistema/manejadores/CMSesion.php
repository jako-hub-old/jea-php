<?php
/**
 * Esta clase se encarga de manejar las sesiones de la aplicación
 * @package sistema.manejadores
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
class CMSesion {
    /*************************
     * Estados de la sesión  *
     *************************/
    const CERRADA = 1;
    const INICIADA = 2;
    const _ID_NOTI_ = '_NOTIFICACIONES_';
    
    /**
     * Hash que identifica la aplicación
     * @var string 
     */
    private $idSesion;
    /**
     * Estado actual de la sesión
     * @var int 
     */
    private $estado;
    
    public function __construct() {
        # se establece un id para la sesión de cada aplicación
        $this->idSesion = Sistema::apl()->ID;
        # si la sesión no ha iniciado la iniciamos
        if(!isset($_SESSION)){
            session_start();
        }
        # si la sesión de la aplicación no está registrada se registra
        if(!isset($_SESSION[$this->idSesion])){
            $_SESSION[$this->idSesion] = [];
        }
        # si no está registrado el array de notificaiones lo registramos,
        # esto nos ahorra validar a la hora de preguntar por notificaciones
        if(!isset($_SESSION[$this->idSesion][self::_ID_NOTI_])){
            $_SESSION[$this->idSesion][self::_ID_NOTI_] = [];
        }
        
        $this->estado = session_status();
    }   
    
    /**
     * Esta función permite iniciar la sesión de manera manual, ya que
     * al instanciar esta clase se inicia la sesión
     */
    public function iniciar(){
        if($this->estado === self::CERRADA){
            $this->estado = self::INICIADA;
            session_start();
            $this->limpiarSesion();
        }
    }    
    
    /**
     * Esta función permite cerrar la sesión, no se destruye, se limpia, debido
     * a que varias apliaciones podrían estar usando la sesión
     */
    public function cerrar(){
        if($this->estado === self::INICIADA){
            $this->estado = self::CERRADA;
            $this->limpiarSesion();
        }
    }
    
    /**
     * Esta función deja la sesión de la aplicación lista para usarse
     */
    public function limpiarSesion(){
        $_SESSION[$this->idSesion] = array();
    }
    
    /**
     * Esta función retorna el estado de la sesión
     * @return int
     */
    public function getEstado(){
        return $this->estado;
    }
    
    /**
     * Esta función permite agregar una atributo a la sesión
     * @param string $nombre
     * @param mixed $valor
     * @return boolean
     */
    public function setAtributo($nombre, $valor){
        if(key_exists($this->idSesion, $_SESSION)){
            $_SESSION[$this->idSesion][$nombre] = $valor;
            return true;
        }
        return false;
    }
    
    /**
     * Esta función permite obtener un atributo de la sesión por su nombre
     * Si el atributo no esta definido en la sesión se retorna falso
     * @param string $nombre
     * @return boolean
     * @return string
     */
    public function getAtributo($nombre){
        if(key_exists($nombre, $_SESSION[$this->idSesion])){
            return $_SESSION[$this->idSesion][$nombre];
        }
        return false;
    }
    
    /**
     * Esta función permite borrar un atributo de la sesión
     * @param string $nombre
     */
    public function borrarAtributo($nombre){
        if(key_exists($nombre, $_SESSION[$this->idSesion])){
            unset($_SESSION[$this->idSesion][$nombre]);
        }
    }
    
    /**
     * Esta función permite guardar notificaciones, las notificaciones son 
     * distintas de los atributos ya que al obtenerlas estas no continuan existiendo
     * en la sesión, son eliminadas tanpronto como se obtiene su valor, si 
     * se desea verificar si existe una notificación use la función existeNotificacion()
     * @param string $nombre
     * @param mixed $valor
     */
    public function setNotificacion($nombre, $valor){
        if(key_exists($this->idSesion, $_SESSION)){
            $_SESSION[$this->idSesion][self::_ID_NOTI_][$nombre] = $valor;
        }
    }
    
    /**
     * Esta función permite obtener una notificación usando su nombre, al obtener
     * una notificación esta será eliminada de la sesión de inmediato
     * @param string $nombre
     * @return boolean
     */
    public function getNotificacion($nombre){
        if(key_exists($this->idSesion, $_SESSION)){
            $notificacion = $_SESSION[$this->idSesion][self::_ID_NOTI_][$nombre];
            unset($_SESSION[$this->idSesion][self::_ID_NOTI_][$nombre]);
            return $notificacion;
        }
        return false;
    }
    
    /**
     * Esta función permite obtener todas las notificaciones registradas
     * @return boolean
     * @return array
     */
    public function getNotificaciones(){
        if(isset($_SESSION[$this->idSesion][self::_ID_NOTI_])){
            return $_SESSION[$this->idSesion][self::_ID_NOTI_];
        }
        return false;
    }
    
    /**
     * Esta función permite verificar si una notificación existe
     * @param string $nombre
     * @return boolean
     */
    public function existeNotificacion($nombre){
        return key_exists($nombre, $_SESSION[$this->idSesion][self::_ID_NOTI_]);
    }    
}