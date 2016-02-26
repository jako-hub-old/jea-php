<?php
/**
 * Esta clase es el asistente para guardar cualquier archivo en el servidor
 * funciona perfectamente con imagenes y adicionalmente permite craer thumbs para 
 * las imagenes guardadas
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @package sistema.web.asistentes
 * @version 1.0.0
 * @copyright (c) 2016, jakop 
 */
class CArchivoCargado {
    
    const NINGUNO = 0;
    const TAMANIO_MAXIMO_SERVIDOR = 1;
    const TAMANIO_MAXIMO_FORM = 2;
    const ERROR_PARCIAL = 3;
    const NO_SE_CARGO = 4;
    const SIN_DIR_TEMPORAL = 6;
    const NO_SE_PUEDE_GUARDAR = 7;        
    /**
     * Nombre del archivo cargado
     * @var string 
     */
    private $nombre;
    /**
     * Tipo de archivo cargado
     * @var string 
     */
    private $tipo;
    /**
     * Ubicación temporal del archivo cargado
     * @var string 
     */
    private $ubicacion;
    /**
     * Código de error 
     * @var int 
     */
    private $error;
    /**
     * Peso del archivo en bytes
     * @var float 
     */
    private $peso;
    /**
     * Ruta en que se guardarán los archivos por defecto
     * @var string 
     */
    private $destinoPorDefecto;
    
    private $rutaDeGuardado;
    private $nombreDeGuardado;
    
    
    
    /**
     * @param string $dd ruta destino por defecto
     */
    private function __construct() {
        $this->destinoPorDefecto = Sistema::resolverRuta('!raiz.publico.archivos');
    }
    
    /**
     * Esta función permite crar una instancia de una imagen cargada usando su nombre
     * 
     * @param string $nombre
     * @return \CArchivoCargado
     */
    public static function instanciarPorNombre($nombre = ''){
        if(isset($_FILES[$nombre]) && is_array($_FILES[$nombre])){
            $archivo = $_FILES[$nombre];
            $instancia = new CArchivoCargado();
            $instancia->nombre = $archivo['name'];
            $instancia->tipo = $archivo['type'];
            $instancia->ubicacion = $archivo['tmp_name'];
            $instancia->error = $archivo['error'];
            $instancia->peso = $archivo['size'];
            return $instancia;
        }
        return null;
    }
    
    /**
     * Esta función permite obtener varios archivos por su nombre
     * @param string $nombre
     * @return CArchivoCargado[]
     */
    public static function instanciarTodasPorNombre($nombre = ''){
        if(isset($_FILES[$nombre]) && is_array($_FILES[$nombre])){
            $instancias = [];
            foreach($_FILES[$nombre]['name'] AS $clave=>$valor){
                $instancia = new CArchivoCargado();
                $instancia->nombre = $valor;
                $instancia->tipo = $_FILES[$nombre]['type'][$clave];
                $instancia->ubicacion = $_FILES[$nombre]['tmp_name'][$clave];
                $instancia->error = $_FILES[$nombre]['error'][$clave];
                $instancia->peso = $_FILES[$nombre]['size'][$clave];
                $instancias[] = $instancia;
            }
            return $instancias;
        }
        return null;
    }
    
    /**
     * Esta función permite guardar un archivo recien subido al servidor
     * @param string $rutaDestino
     * @param string $nombreArchivo
     * @return boolean
     * @throws CExAplicacion
     */
    public function guardar($rutaDestino = '', $nombreArchivo = ''){
        $nombre = $nombreArchivo == ''? $this->nombre : $nombreArchivo . '.' . $this->getExtension();
        $destino = ($rutaDestino == ''? $this->destinoPorDefecto : $rutaDestino);
        
        if(!is_dir($destino)){
            throw new CExAplicacion("No existe la ruta de destino <b>$destino</b>");
        }
        # Esta ruta y el nombre permitirán que sea generado un thumbnail
        $this->rutaDeGuardado = $destino;
        $this->nombreDeGuardado = $nombre;
        
        return move_uploaded_file($this->ubicacion, $destino . DS . $nombre);
    }
    
    /**
     * Esta función permite generar un thumb nail apartir de la imagen guardada,
     * Nota: Esta función solo debe llamarse despues de guardar una imagen, 
     * no funciona con archivos
     * @param string $rutaDestino
     * @param array $config
     * @return boolean
     */
    public function thumbnail($rutaDestino = "", $config = []){
        # configuración
        $pre = isset($config['pre']) ? $config['pre'] : 'tmb_';
        $tamanio = isset($config['tamanio'])? $config['tamanio'] : 100;
        $x = isset($config['x'])? $config['x'] : 0;
        $y = isset($config['y'])? $config['y'] : 0;
        $tipo = isset($config['tipo'])? $config['tipo'] : 'jpg';
        $calidad = isset($config['calidad'])? $config['calidad'] : 50;
        
        # rutas 
        $rutaOrg = $this->rutaDeGuardado;
        $nGuardado = str_replace('.' . $this->getExtension(), '', $this->nombreDeGuardado);
        
        # si la imagen original no existe
        if(!file_exists($rutaOrg . DS . $this->nombreDeGuardado)){ return false; }
        
        $info = getimagesize($rutaOrg . DS . $this->nombreDeGuardado);
        
        $marco = $info[0] <= $info[1]? $info[0] : $info[1];
        
        $imgOrg = $this->getImagenFuente($info, $rutaOrg . DS . $this->nombreDeGuardado);
        
        $thumb = imagecreatetruecolor($tamanio, $tamanio);
        
        if(isset($config['autocentrar']) && $config['autocentrar'] == true){
            $this->autocentrarThumb($x, $y, $info[0], $info[1], $marco);
        }
        
        imagecopyresized($thumb, $imgOrg, 0, 0, $x, $y, $tamanio, $tamanio, $marco, $marco);
        
        $destino = $rutaDestino == ''? $this->rutaDeGuardado : $rutaDestino;
        
        return $this->guardarThumb($thumb, $destino . DS . $pre.$nGuardado, $tipo, $calidad);
    }
    
    /**
     * Esta función permite autro centrar el thumb generado
     * @param float $x
     * @param float $y
     * @param float $ancho
     * @param float $alto
     * @param float $tamanio
     */
    private function autocentrarThumb(&$x, &$y, $ancho, $alto, $tamanio){
        if($ancho > $alto){
            $x = ($ancho / 2) - ($tamanio / 2);
            $y = 0;
        } else if($ancho < $alto){
            $x = 0;
            $y = ($alto / 2) - ($tamanio / 2);
        } else {
            $x = 0;
            $y = 0;
        }
    }
    
    /**
     * Esta función permite guardar un thumbnail
     * @param resource $thumb
     * @param string $ruta
     * @param string $tipo [jpg/png/gif]
     * @param int $calidad
     * @return boolean
     */
    private function guardarThumb($thumb, $ruta, $tipo = 'jpg', $calidad = 50){
        switch ($tipo){
            case 'jpg' : 
                return imagejpeg($thumb, "$ruta.jpg", $calidad);
            case 'png' :
                return imagepng($thumb, "$ruta.png",  $calidad > 9? 0 : $calidad);
            case 'gif' : 
                return imagegif($thumb, "$ruta.gif");
            default :
                return imagejpeg($thumb, "$ruta.jpg", $calidad);
        }
    }
    
    /**
     * Esta función permite obtener una imagen fuente para generar un thumb
     * @param type $info
     * @param type $rutaArchivo
     * @return resource
     * @return boolean
     */
    private function getImagenFuente($info, $rutaArchivo){
        switch ($info['mime']){
            case 'image/png' : return imagecreatefrompng($rutaArchivo);
            case 'image/jpeg' : return imagecreatefromjpeg($rutaArchivo);
            case 'image/gif' : return imagecreatefromgif($rutaArchivo);
            default : return false;
        }        
    }
    
    /**
     * Esta función retorna el nombre del archivo cargado
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Esta función retorna el tipo de archivo cargado
     * @return string
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * Esta función retorna la ruta temporal donde se encuentra el archivo guardado
     * @return string
     */
    public function getUbicacion() {
        return $this->ubicacion;
    }

    /**
     * Esta función retorna el código de error que ocurrió al subir el archivo
     * @return int
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Esta función permite obtener el tamaño del archivo cargado en distintas unidades
     * @param string $unidades [kb/mb/gb]
     * @return float
     */
    public function getPeso($unidades = 'mb') {
        switch ($unidades){
            case 'kb' : return number_format($this->peso / 1024, 2);
            case 'mb' : return number_format($this->peso / (1024 * 2), 1);
            case 'gb' : return number_format($this->peso / (1024 * 3), 1);
            default : return $this->peso;
        }
    }
    
    /**
     * Esta función permite obtener la extensión del archivo guardado
     * @return string
     */
    public function getExtension(){
        $partes = explode('.', $this->nombre);        
        return end($partes);
    }

    /**
     * Esta función permite obtener la ruta por defecto del servidor donde se guardarán las imagenes
     * @return string
     */
    public function getDestinoPorDefecto() {
        return $this->destinoPorDefecto;
    }
    /**
     * Esta función retorna la ruta donde se almacenó un archivo despues de llamar 
     * la función guardar
     * @return string
     */
    public function getRutaGuardado(){
        return $this->rutaDeGuardado;
    }
    
    /**
     * Esta función permite obtener un mensaje más directo del error que sucedió al subir el archivo
     * @return string
     */
    public function getMensajeError(){
        switch ($this->error){
            case self::NINGUNO: return 'Se subió correctamente el archivo';
            case self::TAMANIO_MAXIMO_SERVIDOR : return "Se excedió el tamaño definido en el servidor";
            case self::TAMANIO_MAXIMO_FORM : return "Se excedió el tamaño definido en el formulario";
            case self::NO_SE_CARGO : return "No se cargó ningún archivo";
            case self::SIN_DIR_TEMPORAL : return "No existe el directorio temporal del servidor";
            case self::NO_SE_PUEDE_GUARDAR : return "No se pudo escribir en el directorio temporal";
            default :
                return "No hay error para reportar";
        }
    }

    /**
     * Esta función permite setear una ruta por defecto para guardar las imagenes
     * @param string $destinoPorDefecto
     */
    function setDestinoPorDefecto($destinoPorDefecto) {
        $this->destinoPorDefecto = $destinoPorDefecto;
    }
}
