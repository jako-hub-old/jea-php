<?php
/**
 * Esta interfaz contiene todas las funciones que deben de ser implementadas 
 * por un conector de base de datos
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
interface IConectorBaseDeDatos {
    public static function conectar();
    public static function desconectar();
    public static function liberarMemoria();
    public static function ejecutarConsulta($consulta);
    public static function traerSiguiente();
    public static function ultimoId();
    public static function filasAfectadas();
}
