<?php
/**
 * Esta clase simplifica la funcionalidad del modelo base, haciendo más facil 
 * @package sistema.basededatos
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.2
 * @copyright (c) 2015, jakop
 */
abstract class CModelo extends CBaseModelo{
    /**
     * Objeto encargado del filtro del modelo
     * @var CFiltroModelo 
     */
    private $_filtro;
    /**
     * Indica si ocurrió un error al ejecutar los filtros
     * @var boolean 
     */
    private $_error = false;
    /**
     * Lista de errores generados al ejecutar los filtros
     * @var array
     */
    private $_errores = [];
    
    /**
     * Esta función debe ser implementada para devolver el nombre de la tabla
     * que representa el modelo
     * @return string Retorna el nombre de la tabla que representa el modelo
     */
    public abstract function tabla();
    
    /**
     * Esta función debe ser implementada para devolver el un array con las 
     * columnas de la tabla representada
     */
    public abstract function atributos();
    
    /**
     * Esta función puede ser sobreescrita para definir los filtros que aplican
     * @return array
     */
    public function filtros(){
        return [];
    }
        
    /**
     * Esta función puede ser sobrecargada para retornar las etiquetas
     * o alias para cada atributo del modelo
     * @return array
     */
    public function etiquetasAtributos(){
        return [];
    }
    
    /**
     * Esta función permite obtener el alias (Nombre de etiqueta) para un campo
     * @param string $atributo
     * @return string
     */
    public function obtenerEtiqueta($atributo = ''){
        $atributos = $this->etiquetasAtributos();
        return isset($atributos[$atributo])? 
            $atributos[$atributo] : 
            $atributo;
    }
    
    /**
     * Esta función retorna un array con instancias del modelo
     * @param array $criterio
     * @return CModelo[]
     */
    public function listar($criterio = []){
        return $this->_encontrarTodos($criterio);
    }
    
    /**
     * Esta función permite obtener el conteo de los registros en la tabla
     * @param array $criterios
     * @return int
     */
    public function contar($criterios = []){
        return $this->_contar($criterios);
    }
    
    /**
     * Esta función retorna una instancia del modelo 
     * @param array $criterio
     * @return CModelo
     */
    public function primer($criterio = []){
        $criterio['limit'] = 1;
        $resultados = $this->_encontrarTodos($criterio);        
        return count($resultados) > 0? $resultados[0] : null;
    }
    
    /**
     * Esta función retorna una instancia del modelo usando su clave primaria
     * @param string $pk
     * @return CModelo
     */
    public function porPk($pk){
        $criterio['where'] = "$this->pk='$pk'";
        $resultados = $this->_encontrarTodos($criterio);
        return count($resultados) > 0? $resultados[0] : null;
    }    
    
    public function antesDeGuardar(){}
    
    public function antesDeEliminar(){}    
    
    /**
     * Esta función permite guardar un modelo, si el modelo ya existe se actualiza
     * @return boolean
     */
    public function guardar(){
        # se puede aprovechar esta función para procesar los campos antes de guardar
        $this->antesDeGuardar();
        
        $this->cargarFiltro();
        $this->_error = $this->_filtro->ejecutarFiltros();
        # si ocurrió un error al ejecutar los filtros no guardamos
        if($this->_error){return false;}
        
        if($this->nuevo){
            return $this->_insertar();
        } else {
            return $this->_actualizar();
        }
    }
    
    /**
     * Esta función permite eliminar un modelo
     * @return boolean
     */
    public function eliminar(){
        $this->antesDeEliminar();
        return $this->_eliminar();
    }    
    
    /**
     * Esta función permite cargar la clase filtro designada al modelo
     */
    public function cargarFiltro(){
        if($this->_filtro == null){
            $this->_filtro = new CFiltro($this);
        }
    }
    
    /**
     * Esta función permite setear el campo errores, este campo puede ser usado
     * para almacenar un log de errores que ocurrieron durante un proceso con el modelo
     * @param array $errores
     */
    public function setErrores($errores){
        $this->_errores = $errores;
    }
    
    /**
     * Esta función devuelve el array con el log de errores del modelo
     * @return array
     */
    public function getErrores(){
        return $this->_errores;
    }
    
    /**
     * Esta función devuelve true si hubo algún error en el modelo o false si no
     * @return boolean
     */
    public function hayError(){
        return $this->_error;
    }
    
    /**
     * Esta función retorna una instancia del modelo
     * @param string $clase
     * @return \CModelo
     */
    public static function modelo($clase = __CLASS__){
        if(class_exists($clase)){
            return new $clase();
        } else {
            return null;
        }
    }
}
