<?php echo "<?php\n"; ?>
/**
 * Esta claes es el controlador principal del módulo (Puede ser sustituido)
 * @autor
 * @version 1.0.0
 */
class CtrlPrincipal extends CControlador{    
    /**
     * Esta función muestra la vista de inicio del controlador
     */
    public function accionInicio(){
        $this->mostrarVista("inicio");
    }
}