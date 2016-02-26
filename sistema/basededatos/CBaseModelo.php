<?php
/**
 * Esta clase es la base para todos los modelos, prepara las consultas las ejecuta
 * y devuelve los resultados generados por las consultas
 * @package sistema.basededatos
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2015, jako 
 */

abstract class CBaseModelo {
    const PERTENECE_A = 1;
    const CONTENGAN_A = 2;
    
    /************************************************
     *               Tipos de consulta              *
     ************************************************/
    const _CONSULTA_ = 1;
    const _INSERCION_ = 2;
    const _ACTUALIZACION_ = 3;
    const _ELIMINACION_ = 4;
    
    /**
     * Alias usado para las tablas en las consultas
     * @var string 
     */
    private $_alias;
    /**
     * Indica si el modelo es nuevo registro o ya existente
     * @var boolean 
     */
    private $_nuevo = true;
    
    /**
     * Contiene los atributos del modelo que representa la tabla
     * @var array 
     */
    private $_atributos = [];
    /**
     * Contiene las relaciones definidas en el modelo
     * @var array 
     */
    private $_relaciones = [];
    /**
     * Contiene los modelos con que se relaciona el modelo actual
     * @var array 
     */
    private $_relacionesModelos = [];
    
    /**
     * Guardará el nombre del campo que es la clave primaria
     * @var string 
     */
    private $_pk;
    
    public function __construct() {        
        $this->construirColumnas();
        $this->_alias = Sistema::apl()->bd->controlador->alias;
    }
    
    /**
     * Esta función construye los atributos del modelo y define cual es el atributo
     * primary key, adicionalmente si en el array de columnas se encuentra definido
     * el valor por defecto['def'], este será asignado
     */
    private function construirColumnas(){
        $columnas = $this->atributos();
        foreach ($columnas AS $clave=>$valor){
            # verificamos si la clave del array de columnas es entero o es string
            # si es string significa que probablemente el valor es array y tiene otras caracteristicas
            if(is_string($clave)){ 
                $columna = $clave;                 
            } else { 
                $columna = $valor; 
            }
            # buscamos la primary key
            if(is_array($valor) && array_search('pk', $valor) !== false){ 
                $this->_pk = $clave;
            }
            # buscamos si hay valores por defecto
            if(is_array($valor) && key_exists('def', $valor)){
                $valorPorDefecto = $valor['def'];
            } else {
                $valorPorDefecto = null;
            }
            # obtenemos las relaciones definidas en el modelo
            $this->_relaciones = $this->relaciones();
            # creamos los atributos del modelo
            $this->_atributos[$columna] = $valorPorDefecto;
        }
    }
    
    /**
     * Esta función puede ser sobreescrita para retornar las relaciones comprendidas en el modelo
     * @return array
     */
    protected function relaciones(){
        return [];
    }
        
    /************************************************
     *               Funciones magicas              *
     ************************************************/    
    public function __set($nombre, $valor) {
        if(method_exists($this, "set" . ucfirst($nombre))){
            $this->{"set".ucfirst($nombre)}($valor);
        } else if(key_exists($nombre, $this->_atributos)){
            $this->_atributos[$nombre] = $valor;
        }
    }
    
    public function __get($nombre){
        if(method_exists($this, "get" . ucfirst($nombre))){
            # si el atributo pedido tiene una función get creada, la ejecutamos
            return $this->{"get".ucfirst($nombre)}();
        } else if(key_exists($nombre, $this->_atributos)) {
            # si el atributo pedido se encuentra en el array de atributos, lo devolvemos
            return $this->_atributos[$nombre];
        } else if(key_exists($nombre, $this->_relaciones) && !key_exists($nombre, $this->_relacionesModelos)){
            # si el atributo pedido se encuentra en las relaciones, pero no ha sido cargado, lo cargamos
            $this->cargarRelacion($nombre, $this->_relaciones[$nombre]);
            return $this->_relacionesModelos[$nombre];
        } else if(key_exists($nombre, $this->_relacionesModelos)){
            # si el atributo se encuentra en las relaciones cargadas, la retornamos
            return $this->_relacionesModelos[$nombre];
        }
    }
    
    /**
     * Esta función se encarga de setear en el modelo la relación
     * @param string $nombre nombre del campo solicitado
     * @param array $relacion relación establecida en el modelo
     * @throws CExAplicacion Si la relación no fue definida correctamente [tipo, modelo, campo fk]
     */
    private function cargarRelacion($nombre, $relacion){
        # la configuración de la relación debe tener tres parametros
        if(count($relacion) < 3){
            throw new CExAplicacion("La relación especificada no es valida <b>Faltan parámetros</b>");
        }
        
        $nModelo = $relacion[1];
        $campo = $relacion[2];
        if($relacion[0] === self::PERTENECE_A){
            # si la relación es tipo PERTENECE A
            $modelo = $nModelo::modelo()->porPk($this->_atributos[$campo]);
            $this->_relacionesModelos[$nombre] = $modelo;
        } else if($relacion[0] == self::CONTENGAN_A){
            # si la relación es de tipo CONTENGAN A
            $modelos = $nModelo::modelo()->listar([
                'where' => "$campo='" . $this->_atributos[$this->_pk] . "'",
            ]);
            $this->_relacionesModelos[$nombre] = $modelos;
        }
    }

    /************************************************
     *       Funciones de obtención de variables    *
     ************************************************/
    
    public function getPk(){
        return $this->_pk;
    }
    
    public function getId(){
        return $this->_atributos[$this->_pk];
    }


    public function getNuevo(){
        return $this->_nuevo;
    }        

    /************************************************
     *             Funciones de consultas           *
     ************************************************/
    
    /**
     * Esta función prepara y ejectuta la consulta para traer
     * todos los registros
     * @param array $criterio
     * @return CModelo[]
     */
    protected function _encontrarTodos($criterio = []){
        $this->setCriterios($criterio);
        $modelos = [];
        $registros = $this->ejecutar();
        $claseInvocada = get_called_class();
        foreach($registros AS $atributos){
            $modelo = new $claseInvocada();
            $modelo->atributos = $atributos;
            $modelo->_nuevo = false;
            $modelos[] = $modelo;
        }
        return $modelos;
    }
    
    /**
     * Esta función permite contar los registros de una tabla
     * @param array $criterio
     * @return int
     */
    protected function _contar($criterio = []){
        $criterio['select'] = 'COUNT(*) AS total';
        $this->setCriterios($criterio);
        $registros = $this->ejecutar();
        if(count($registros) > 0){
            return intval($registros[0]['total']);
        }
        return 0;
    }
    
    /**
     * Esta función permite preparar y ejecutar la consulta para la inserción de 
     * un nuevo registro
     * @return boolean
     */
    protected function _insertar(){
        $columnas = $this->_atributos;
        unset($columnas[$this->_pk]);
        $criterios = [
            'columnas' => implode(', ', array_keys($columnas)),
            'valores' => implode(', ', array_map(
                        function($val){ return $val == ''? 'null' :  "'$val'"; },
                        $columnas
                    )),
        ];        
        $this->setCriterios($criterios);
        $resultado = $this->ejecutar(self::_INSERCION_);
        if($resultado){
            $this->_atributos[$this->_pk] = Sistema::apl()->bd->controlador->ultimoId();
            $this->_nuevo = false;
        }
        return $resultado;
    }
    
    /**
     * Esta función permite preparar y ejecutar la consulta para actualizar un registro
     * @return boolean
     */
    protected function _actualizar(){
        $columnas = $this->_atributos;
        unset($columnas[$this->_pk]);
        $criterio = [
            'columnas' => implode(', ', array_map(
                        function($k, $v){ return "$this->_alias.`$k`=" . ($v == ""? 'null' : "'$v'") . ""; },
                        array_keys($columnas),
                        $columnas
                    )),
            'where' => "$this->_alias.$this->_pk=".$this->_atributos[$this->_pk]
        ];
                        
        $this->setCriterios($criterio);
        return $this->ejecutar(self::_ACTUALIZACION_);
    }
    
    /**
     * Esta función permite preparar y ejecutar la consulta para eliminar un registro
     * @return boolean
     */
    public function _eliminar(){
        $criterio = [
            'where' => "$this->_pk=".$this->_atributos[$this->_pk]
        ];
        $this->setCriterios($criterio);
        return $this->ejecutar(self::_ELIMINACION_);
    }
    
    /**
     * Esta función permite setear los criterios para crear la consulta
     * @param array $criterios
     */
    private function setCriterios($criterios = []){
        Sistema::apl()->bd->controlador->tabla = $this->tabla();
        Sistema::apl()->bd->controlador->setCriterios($criterios);
    }
    
    /**
     * Esta función permite ejecutar una consulta
     * @param int $tipo tipod e consulta a ejecutar
     * @return mixed
     * @throws CExAplicacion Si la operación solicitada no existe
     */
    private function ejecutar($tipo = self::_CONSULTA_){
        switch ($tipo){
            case self::_CONSULTA_: 
                return Sistema::apl()->bd->controlador->consultar();
            case self::_INSERCION_: 
                return Sistema::apl()->bd->controlador->insertar();                
            case self::_ACTUALIZACION_ : 
                return Sistema::apl()->bd->controlador->actualizar();
            case self::_ELIMINACION_:
                return Sistema::apl()->bd->controlador->eliminar();
            default : 
                throw new CExAplicacion("Operación no soportada");
        }
    }
    
    /************************************************
     *       Funciones de obtención de variables    *
     ************************************************/
    /**
     * Esta función permite setear todos los atributos del modelo de golpe
     * @param array $atributos
     */
    private function setAtributos($atributos){
        foreach($atributos AS $atributo=>$valor){
            if(key_exists($atributo, $this->_atributos)){
                $this->_atributos[$atributo] = $valor;
            }
        }
    }            
}