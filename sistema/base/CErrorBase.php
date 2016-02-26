<?php
/**
 * De esta clase extenderán todas las clases que manejen errores
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
abstract class CErrorBase {
    /**
     * Mensaje producido por el error
     * @var string 
     */
    protected $mensaje;
    /**
     * Linea donde se produjo el error
     * @var int 
     */
    protected $linea;
    /**
     * Nombre del archivo donde ocurrió el error (contiene la ruta)
     * @var string 
     */
    protected $archivo;
    /**
     * Código producido por el error
     * @var int 
     */
    protected $codigo;
    /**
     * Rastreo del posible error
     * @var array 
     */
    protected $rastreo;
    /**
     * Máximo de pasos a incluir en el rastreo
     * @var int
     */
    protected $limiteRastrio;
    /**
     * Titulo del error generado
     * @var string 
     */
    protected $titulo;
    /**
     * Html generado por la vista del error
     * @var string 
     */
    protected $contenido;
    /**
     * Ruta donde se encuentran las vistas de los errores
     * @var string 
     */
    protected $rutaVistas;
    
    /**
     * Se puede asignar un array al constructor de un error para setear
     * de manera rápida todos sus atributos
     * @param array $parametros
     */
    public function __construct($parametros = []) {
        if(is_array($parametros) && count($parametros)){
            $this->setParametros($parametros);
        }
        $this->rutaVistas = '!sistema.vistas.errores';
    }
    
    /**
     * Esta función permite asignar todos los parametros a la clase usando un array
     * @param array $p parametros
     */
    private function setParametros($p = []){
        foreach ($p AS $n=>$v){
            if(property_exists($this, $n)){
                $this->$n = $v;
            }
        }        
    }
    
    /**
     * Esta función permite cargar el contenido de la vista de error
     * @param string $vista
     * @return boolean si logra renderizar algo true
     */
    protected function mostrarError($vista = ''){
        $ruta = realpath(Sistema::resolverRuta($this->rutaVistas).DS.$vista.'.php');
        if($ruta === false){
            return false;
        }
        # limpiamos el buffer de lo que se haya impreso en pantalla
        ob_end_clean();
        # limpiamos los recursos agregados
        Sistema::apl()->mRecursos->limpiarRecursos();        
        $this->contenido = $this->cargarVista($ruta);
        Sistema::apl()->mRecursos->incluirRecursos($this->contenido);
        echo $this->contenido;
        
        return true;
    }
    
    /**
     * Esta función permite cargar el contenido html de una vista en una variable
     * @param string $vista
     * @return string
     */
    private function cargarVista($vista){
        ob_start();
        @include $vista;
        return ob_get_clean();
    }
    
    /**
     * Esta función pemite visualizar el archivo donde sucedió el error
     * @param string $archivo
     * @param int $linea
     * @return string
     */
    private function verError($archivo, $linea){
        $codigo = '';
        if(file_exists($archivo) && ($lineas = @file($archivo)) !== false && 
                ($totalLineas = count($lineas)) >= $linea){
            $lineaConError = $linea - 1;
            $limiteSup = ($lineaConError - 10 < 0) ? 0 : $lineaConError - 10;
            $limiteInf = ($lineaConError + 10 > $totalLineas)? $totalLineas : $lineaConError + 10;            
            for($cont = $limiteSup; $cont < $limiteInf; $cont ++) {
                $numLinea = '<span class="linea-codigo">' . str_pad(($cont + 1), 3, '0', STR_PAD_LEFT) . '</span>';
                if($cont === $lineaConError){
                    $codigo .= '<span class="linea-con-error">'.$numLinea.  htmlentities($lineas[$cont]) . '</span>';
                }else{
                    $codigo .= $numLinea.  htmlentities($lineas[$cont]);
                }
            }
        }
        return '<pre>'.$codigo.'</pre>';
    }
    
    
    
}
