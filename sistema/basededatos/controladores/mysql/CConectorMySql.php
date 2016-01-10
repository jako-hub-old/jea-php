<?php
/**
 * Esta clase ees el controlador de la base de datos mysql, contiene toda
 * la lógica para realizar operaciones especificamente con mysql
 * @package sistema.basededatos.controladores.mysql
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2015, jakop
 */
final class CConectorMySql extends CConectorBaseDeDatos 
    implements IConectorBaseDeDatos {
    
    private $recursoBd;
    private $resultados;
    /**
     * Única instancia dle controlador
     * @var CControladorMySql 
     */
    private static $instancia;
    
    private function __construct($conf) {
        $this->usuario = isset($conf['usuario'])? $conf['usuario'] : 'root';
        $this->clave = isset($conf['clave'])? $conf['clave'] : '';
        $this->puerto = isset($conf['puerto'])? $conf['puerto'] : '3306';
        $this->servidor = isset($conf['servidor'])? $conf['servidor'] : '127.0.0.1';
        $this->baseDeDatos = isset($conf['bd'])? $conf['bd'] : '';
        $this->charset = isset($conf['charset'])? $conf['charset'] : 'utf-8';
    }
    
    public function __destruct() {
        $this->desconectar();
    }
    
    public static function iniciar($conf){
        if(self::$instancia == null){
            self::$instancia = new CConectorMySql($conf);
        }
    }
   
    /**
     * Esta función permite conectarse al servidor de base de datos mysql
     * @return mixed
     * @throws CExBaseDeDatos
     */
    public static function conectar() {
        self::$instancia->recursoBd = mysqli_connect(
                    self::$instancia->servidor,
                    self::$instancia->usuario, 
                    self::$instancia->clave, 
                    self::$instancia->baseDeDatos, 
                    self::$instancia->puerto);
        if(!self::$instancia->recursoBd){
            throw new CExBaseDeDatos("Error al conectarse a la base de datos: ".  mysqli_connect_error());
        }
        # definimos el charset a usar por mysql
        mysqli_set_charset(self::$instancia->recursoBd, self::$instancia->charset);
        return self::$instancia->recursoBd;
    }
    
    /**
     * Esta función permite cerrar la conexión con el servidor de base de datos
     * @return boolean
     */
    public static function desconectar() {
        if(self::$instancia->recursoBd  !== null){
            mysqli_close(self::$instancia->recursoBd);
            self::$instancia->recursoBd = null;
            return true;
        }
        return false;
    }

    /**
     * Esta función permite ejecutar una consulta
     * @param type $consulta
     * @return mixed
     * @throws CExBaseDeDatos 
     */
    public static function ejecutarConsulta($consulta) {
        self::conectar();
        self::$instancia->resultados = mysqli_query(self::$instancia->recursoBd, $consulta);
        if(!self::$instancia->resultados){
            throw new CExBaseDeDatos("Error al ejecutar la consulta: " 
                    . mysqli_error(self::$instancia->recursoBd) . " " 
                    . "<br>Consulta ejecutada <br>"
                    . "<b>$consulta</b>"
                    );
        }       
        return self::$instancia->resultados;
    }

    /**
     * Esta función permite obtener el número de filas afectadas por una consulta
     * @return int
     */
    public static function filasAfectadas() {
        return self::$instancia->recursoBd !== null? 
                mysqli_affected_rows(self::$instancia->recursoBd) : 0;
    }

    /**
     * Esta función permite liberar la memoria usada por una consulta
     * @return boolean
     */
    public static function liberarMemoria() {
        if(self::$instancia->recursoBd !== null){
            mysqli_free_result(self::$instancia->resultados);
            return true;
        }
        return false;
    }
    
    /**
     * Esta función permite traer los resultados generados por una consulta en formato array
     * @return boolean
     * @return array
     */
    public static function traerSiguiente() {
        if(self::$instancia->resultados !== null && self::$instancia->resultados !== false){
            $siguiente = mysqli_fetch_array(self::$instancia->resultados, MYSQLI_ASSOC);
            if($siguiente === false || $siguiente === null){
                self::liberarMemoria();
            }
            return $siguiente;
        }
        return false;
    }

    /**
     * esta función retorna el último id insertado
     * @return int
     */
    public static function ultimoId() {
        return self::$instancia->recursoBd !== null? 
                mysqli_insert_id(self::$instancia->recursoBd) : 0;
    }
}
