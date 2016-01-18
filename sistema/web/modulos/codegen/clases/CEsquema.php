<?php
/**
 * Esta claes es un asistente para obtener una descripción de las tablas de la base 
 * de datos
 * @package sistema.web.modulos.codegen
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jako
 */
class CEsquema {
    /**
     * Nombre de la base de datos seleccionada
     * @var string 
     */
    private $bd;
    /**
     * Prefijo establecido para las tablas
     * @var string 
     */
    private $prefijo; 
    
    public function __construct() {
        $this->bd = Sistema::apl()->bd->nombreBD;
        $this->prefijo = Sistema::apl()->bd->prefijo;
    }
    
    /**
     * Esta función retorna todas las tablas de la base de datos seleccionada
     * @return array
     */
    public function obtenerTablas(){
        $consulta = "SHOW TABLES";
        $resultado = Sistema::apl()->bd->controlador->ejecutarComando($consulta);
        $tablas = [];        
        foreach ($resultado AS $t){
            $n = $t["Tables_in_$this->bd"];
            $tablas[$n] = $n;
        }
        return $tablas;
    }
    
    /**
     * Esta función retorna los atributos o campos de una tabla y su definición
     * en la base de datos
     * @param string $tabla
     * @return array
     */
    public function obtenerAtributos($tabla){
        $consulta = "DESCRIBE `$tabla`";
        $campos = Sistema::apl()->bd->controlador->ejecutarComando($consulta);
        return $campos;
    }
    
    /**
     * Esta función retorna las relaciones que tiene la tabla
     * @param string $tabla
     * @return array
     */
    public function obtenerRelaciones($tabla){
        $consulta = "SELECT " .
                    "CONSTRAINT_NAME AS `fk_name`, " .
                    "TABLE_SCHEMA as `database`, " .
                    "TABLE_NAME AS `table_name`, " .
                    "COLUMN_NAME AS `foreign_key_column`, " .
                    "REFERENCED_TABLE_SCHEMA AS `referenced_database`, " .
                    "REFERENCED_TABLE_NAME AS `referenced_table`, " .
                    "REFERENCED_COLUMN_NAME AS `referenced_column` " . 
            "FROM " . 
                    "information_schema.KEY_COLUMN_USAGE " .
            "WHERE " .
                    "TABLE_SCHEMA = SCHEMA() " .
                    "AND REFERENCED_TABLE_NAME IS NOT NULL " .
                    "AND TABLE_NAME = '$tabla'";
        $relaciones = Sistema::apl()->bd->controlador->ejecutarComando($consulta);
        return $relaciones;
    }
    
    /**
     * Esta función retorna el prefijo selecionado para las tablas
     * @return string
     */
    public function getPrefijo(){
        return $this->prefijo;
    }
}
