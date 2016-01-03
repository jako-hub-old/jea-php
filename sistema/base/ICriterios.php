<?php
/**
 * Esta interfaz contiene todas las funciones que deben de ser implementadas 
 * para construir los criterios de una consulta
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
interface ICriterios {
    public function select($p = '');
    public function join($p = '');
    public function where($p = '');
    public function group($p = '');
    public function order($p = '');
    public function limit($p = '');
    public function offset($p = '');
}
