<?php

/**
 * Esta interfaz la deben de implementar todas las clases que vayan a ser usadas
 * como controlador de bases de datos
 * @package sistema.base
 * @author Jorge Alejandro Quiroz Serna <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 * 
 */

abstract class CConectorBaseDeDatos {
    /**
     * Nombre de usuario de la base de datos
     * @var string 
     */
    protected $usuario;
    /**
     * Contraseña de la base de datos
     * @var string 
     */
    protected $clave;
    /**
     * Puerto para la conexión
     * @var string 
     */
    protected $puerto;
    /**
     * Nombre del servidor al cual se establecerá  la conexión
     * @var string 
     */
    protected $servidor;
    /**
     * Nombre de la base de datos
     * @var string 
     */
    protected $baseDeDatos;
    /**
     * Codificación usada
     * @var string 
     */
    protected $charset = 'utf-8';       
}
