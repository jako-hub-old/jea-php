<?php
/**
 * Esta clase simplifica la funcionalidad del modelo base, haciendo más facil 
 * @package sistema.basededatos
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
abstract class CModelo extends CBaseModelo{
        
    /**
     * Esta función puede ser sobrecargada para retornar las etiquetas
     * o alias para cada atributo del modelo
     * @return array
     */
    public function etiquetasAtributos(){
        return [];
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
    /**
     * Esta función permite guardar un modelo, si el modelo ya existe se actualiza
     * @return boolean
     */
    public function guardar(){
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
        return $this->_eliminar();
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
