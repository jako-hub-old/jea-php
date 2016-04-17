<?php
/**
 * Esta clase representa una excepción común y corriente
 * @package sistema.excepciones
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
class CExcepcion extends CErrorBase{
    protected $excepcion;
    
    public function __construct(Exception $e) {
        parent::__construct(array(
            'mensaje' => $e->getMessage(),
            'codigo' => $e->getCode(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine(),
            'rastreo' => $e->getTrace(),
            'limiteRastreo' => 10,
            'titulo' => property_exists($e, 'titulo')? $e->titulo : 'Excepción',
        ));
        $this->excepcion = $e;
        $produccion = Sistema::apl()->modoProduccion;
        
        if(!$produccion){
            $this->mostrarError('excepcion');
        } else {
            CControlador::redireccionarA('error', '500');
            $this->mostrarError('produccion');
        }
    }
}
