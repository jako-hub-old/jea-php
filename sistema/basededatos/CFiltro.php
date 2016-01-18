<?php
/**
 * Esta clase es la encargada de recoger los filtros especificados en el modelo y 
 * ejecutarlos
 * @package sistema.basesdedatos
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jakop 
 */
class CFiltro {
    /**
     * Representación del modelo al que se le aplican los filtros
     * @var CModelo 
     */
    private $m;
    private $logErrores = [];
    
    public function __construct(&$modelo) {
        $this->m = $modelo;
    }
    
    /**
     * Esta función aplica los filtros al modelo
     * @return boolean
     */
    public function ejecutarFiltros(){
        $reglas = $this->m->filtros();
        # validamos campos requeridos
        $this->validarRequeridos($reglas);
        
        $this->m->setErrores($this->logErrores);
        return count($this->logErrores) > 0;
    }
    
    /**
     * Esta función se ocupa de ejcutar los filtros de campos requeridos
     * @param filtros $r
     * @return boolean
     */
    private function validarRequeridos($r = []){        
        if(!isset($r['requeridos']) || count($r['requeridos']) == 0){
            return false;
        }
        # removemos cualquier espacio de los campos requeridos
        $r['requeridos'] = str_replace(' ', '', $r['requeridos']);
        # separamos los campos requeridos para recorrerlos
        $campos = explode(',', $r['requeridos']);
        $errores = [];
        # recorremos los campos requeridos
        foreach ($campos AS $campo){
            # si un campo requerido está vacio en el modelo, lo agregamos a los errores
            if(trim($this->m->$campo) == ""){
                $errores[] = $campo;
            }
        }
                
        if(count($errores) > 0){
            # llenamos un log de errores que pueda ser obtenido despues
            $this->logErrores['requeridos'] = $errores;
        }               
        
        return count($errores) > 0;
    }
    
}