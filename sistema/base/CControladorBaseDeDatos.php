<?php
/**
 * Esta clase será la base para la creación de las clases que serviran como 
 * controladores de bases de datos
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.2
 * @copyright (c) 2015, jakop
 *
 * @property string $consulta
 * @property string $select
 * @property string $join
 * @property string $where
 * @property string $group
 * @property string $order
 * @property string $limit
 * @property string $offset
 * @property string $tabla
 * @property string $alias
 * @property string $columnas
 * @property string $valores
 * @property string $prefijo
 */
abstract class CControladorBaseDeDatos 
    implements ICriterios{
    
    protected $_tabla;
    protected $_columnas;
    protected $_valores;
    protected $_consulta;
    protected $_alias = 't';
    protected $_prefijo = '';
    
    /************************************************
     *               comandos de sql                *
     ************************************************
     * Estos con los comandos mysql con que puede   *
     * contar todo controlador de base de datos     *
     ************************************************/
    protected $_select = '*';
    protected $_join;
    protected $_where;
    protected $_group;
    protected $_order;
    protected $_limit;
    protected $_offset;
    
    public function __set($nombre, $valor){
        if(method_exists($this, $nombre)){
            $this->$nombre($valor);
        }
    }
    
    public function __get($nombre){
        if(method_exists($this, "get$nombre")){
            return $this->{"get$nombre"}();
        }else{
            throw new CExAplicacion("La propiedad $nombre no esta definida o está protegida");
        }
    }
    
    /**
     * Esta función es usada para setear el nombre de la tabla para 
     * las consultas
     * @param string $tabla
     */
    public function tabla($tabla){
        $this->_tabla = $this->_prefijo.$tabla;
    }
    
    /************************************************
     *            Funciones para el crud            *
     ************************************************/
    public abstract function consultar();

    public abstract function insertar();
    
    public abstract function actualizar();
    
    public abstract function eliminar();
    
    public abstract function ejecutarComando($comando);


    /************************************************
     *        Funciones para armar criterios        *
     ************************************************/
    public function select($p = ''){ $this->_select = $p; }
    public function join($p = ''){ $this->_join = $p; }
    public function where($p = ''){ $this->_where = $p; }
    public function group($p = ''){ $this->_group = $p; }
    public function order($p = ''){ $this->_order = $p; }
    public function limit($p = ''){ $this->_limit = $p; }
    public function offset($p = ''){ $this->_offset = $p; }
    public function columnas($p = ''){ $this->_columnas = $p; }
    public function valores($p = ''){ $this->_valores = $p; }
    public function prefijo($p = ''){ $this->_prefijo = $p; }
    
    /************************************************
     *     Funciones para obtención de variables    *
     ************************************************/
    public function getAlias(){
        return $this->_alias;
    }
    
    
    /************************************************
     *               Otras funciones                *
     ************************************************/
    public abstract function ultimoId();
    
    /**
     * Esta función llama a los metodos para armar los criterios
     * @param array $criterios
     */
    public function setCriterios($criterios = []){
        foreach($criterios AS $criterio => $valor){
            if(method_exists($this, $criterio)){
                $this->$criterio($valor);
            }
        }
    }
    
    /**
     * Esta función limpia todos los campos del controlador, debe usarse 
     * despues de ejecutar una consulta
     */
    public function limpiar(){
        $comandosALimpiar = [
            '_join',
            '_where',
            '_group',
            '_order',
            '_limit',
            '_offset',
        ];
        foreach ($comandosALimpiar AS $nombre){
            $this->$nombre = null;
        }
        $this->_select = '*';
    }    
}