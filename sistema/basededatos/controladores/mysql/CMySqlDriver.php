<?php
/**
 * Esta clase es el controlador de base de datos para MySql, su lógica esta 
 * enfocada a funcionar solo con mysql
 * @package sistema.basesdedatos.controladores.mysql
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
class CMySqlDriver extends CControladorBaseDeDatos{
    
    /**
     * Esta función ejecuta una consulta y retorna un array de arrays
     * con los registros encontrados, cada posición del primer array
     * es un registro, donde cada posición en el array interno es una 
     * columna de la tabla
     * @return array
     */
    public function consultar() {
        $this->_consulta = "SELECT $this->_select "
                . "FROM $this->_tabla AS $this->_alias"
                . (isset($this->_join)? $this->_join : " ")
                . (isset($this->_where)? "WHERE ".$this->_where . " " : "")
                . (isset($this->_group)? "GROUP BY " . $this->_group . " " : "")
                . (isset($this->_order)? "ORDER BY " . $this->_order . " " : "")
                . (isset($this->_limit)? "LIMIT " . $this->_limit . " " : "")
                . (isset($this->_offset)? "OFFSET " . $this->_offset . " " : "");
        $registros = [];        
        CConectorMySql::ejecutarConsulta($this->_consulta);
        while($datos = CConectorMySql::traerSiguiente()){
            if($datos !== false){
                $registros[] = $datos;
            }
        }
        return $registros;
    }
    
    /**
     * Esta función permite insertar un nuevo registro
     * @return booelan
     */
    public function insertar() {
        $this->_consulta = "INSERT INTO $this->_tabla ($this->_columnas) VALUES ($this->_valores)";
        return $this->ejecutarConsulta();
    }
    
    /**
     * Esta función permite actualizar un registro
     * @return boolean
     */
    public function actualizar() {
        $this->_consulta = "UPDATE $this->_tabla $this->_alias SET $this->_columnas WHERE $this->_where";
        return $this->ejecutarConsulta();
    }

    /**
     * Esta función permite eliminar un registro
     * @return boolean
     */
    public function eliminar() {
        $this->_consulta = "DELETE FROM $this->_tabla WHERE $this->_where";        
        return $this->ejecutarConsulta();
    }
    
    /**
     * Esta función permite ejecutar una consulta y obtener su resultado
     * @return mixed
     */
    private function ejecutarConsulta(){
        $resultado = CConectorMySql::ejecutarConsulta($this->_consulta);
        return $resultado;
    }
    
    /**
     * Esta función permite obtener el último Id guardado en mysql
     * @return int
     */
    public function ultimoId() {
        return CConectorMySql::ultimoId();
    }
}
