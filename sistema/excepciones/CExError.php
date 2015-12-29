<?php
/**
 * Esta clase maneja los errores que se puedan producir
 * @package sistema.excepciones
 * @author Jorge Alejandro Quiroz Serna (jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2015, jakop
 */
class CExError extends CErrorBase{
    /**
     * Código del error generado
     * @var int 
     */
    protected $no;
    
    public function __construct($no, $mensaje, $archivo, $linea) {
        $this->no = $no;
        parent::__construct(array(
            'mensaje' => $mensaje,
            'codigo' => $no,
            'archivo' => $archivo,
            'linea' => $linea,
            'rastreo' => debug_backtrace(),
            'limiteRastreo' => 10,
            'titulo' => 'Error',
        ));
        $this->mostrarError('error');
    }
    /**
     * Esta función retorna el tipo de error generado
     * @param int $no
     * @return string
     */
    public function getTipoError($no){
        $error = '';
        switch($no){
            case E_ERROR:               $error = "Error";                  break;
            case E_WARNING:             $error = "Warning";                break;
            case E_PARSE:               $error = "Parse Error";            break;
            case E_NOTICE:              $error = "Notice";                 break;
            case E_CORE_ERROR:          $error = "Core Error";             break;
            case E_CORE_WARNING:        $error = "Core Warning";           break;
            case E_COMPILE_ERROR:       $error = "Compile Error";          break;
            case E_COMPILE_WARNING:     $error = "Compile Warning";        break;
            case E_USER_ERROR:          $error = "User Error";             break;
            case E_USER_WARNING:        $error = "User Warning";           break;
            case E_USER_NOTICE:         $error = "User Notice";            break;
            case E_STRICT:              $error = "Strict Notice";          break;
            case E_RECOVERABLE_ERROR:   $error = "Recoverable Error";      break;
            default:                    $error = "Unknown error ($errno)"; break;
        }
        return '<span class="label label-danger">'.$error.'</span>';
    }
}
