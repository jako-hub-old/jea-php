<?php
/**
 * Esta clase es el asistente para la generación de html
 * @package sistema.web.asistentes
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.1
 * @copyright (c) 2014, jakop
 */
final class CHtml {
    
    # Evitamos que esta clase sea instanciada simplemente haciendo
    # si constructor privado
    private function __construct() {}
    
    /**
     * Esta función permite crear cualquier etiqueta html
     * @param string $etiqueta
     * @param string $contenido
     * @param [] $opciones
     * @param boolean $cierra si la etiqueta cierra o no
     * @return string
     */
    public static function e($etiqueta = 'div', $contenido = '', $opciones = [], $cierra = true){
        $opc = self::crearOpcionesHtml($opciones);
        return "<$etiqueta" . ($opc != ""? " $opc" : "") .">"
                . ($cierra? "$contenido</$etiqueta>" : "");
    }
    
    /**
     * Esta función permite crear etiquetas in cierre
     * @param type $etiqueta
     * @param type $opciones
     * @return string
     */
    public static function ec($etiqueta, $opciones = []){
        return self::e($etiqueta, '', $opciones, false);
    }
    
    /**
     * Esta función permite crear cualquier tipo de input html
     * @param string $tipo
     * @param string $valor
     * @param [] $opciones
     * @return string
     */
    public static function input($tipo = 'text', $valor = '', $opciones = []){
        $opcionesHtml = self::crearOpcionesHtml($opciones);
        return "<input type=\"$tipo\" value=\"$valor\" $opcionesHtml/>";
    }
    
    /**
     * Esta función permite crear un campo de texto html
     * @param string $valor
     * @param array $opciones
     * @return string
     */
    public static function campoTexto($valor, $opciones = []){
        return self::input("text", $valor, $opciones);
    }
    
    /**
     * Esta función permite crear un area de texto html
     * @param string $valor
     * @param array $opciones
     * @return string
     */
    public static function areaTexto($valor, $opciones){
        return self::e('textarea',$valor, $opciones);
    }
    
    /**
     * Esta función permite crear una lista de selección html
     * @param mixed $seleccion
     * @param array $elementos
     * @param array $opciones
     * @return string
     */
    public static function lista($seleccion = '', $elementos = [], $opciones = []){
        $items = [];
        # hayamos si hay valor por defecto
        if(isset($opciones['defecto'])){
            $items[] = self::e('option', $opciones['defecto'], ['value' => '']);
            unset($opciones['defecto']);
        }
        # construimos las opciones
        foreach ($elementos AS $valor=>$texto){
            $opcionesE = ['value' => $valor];
            # la seleccion == '' puede ser igual a cero, por eso toca validar contra nulo
            if($seleccion !== '' && $seleccion !== null && $seleccion == $valor){
                $opcionesE['selected'] = 'selected';
            }
            $items[] = self::e('option', $texto, $opcionesE);
        }
        return self::e('select', implode('', $items), $opciones);
    }
    
    /**
     * Esta función permite craer un botón html
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function boton($nombre, $opciones = []){
        return self::e('button', $nombre, $opciones);
    }
    
    /**
     * Esta función permite crear un botón submit html
     * @param string $nombre
     * @param array $opciones
     * @return string
     */
    public static function botonSubmit($nombre, $opciones = []){
        return self::input("submit", $nombre, $opciones);
    }
    
    /**
     * Esta función permite crear un link (hipervinculo) html
     * @param string $texto
     * @param mixed $url
     * @param array $opciones
     * @return string
     */
    public static function link($texto = '', $url = [], $opciones = []){
        $opciones['href'] = Sistema::apl()->crearUrl($url);
        return self::e('a',$texto, $opciones);
    }
    
    /**
     * Esta función permite codificar todos los caracteres de una cadena, esta
     * codificación permite reemplazar los caracteres html por su valor acsii
     * @param strings $cadena
     * @return string
     */
    public static function codificar($cadena){
        return htmlentities($cadena, ENT_QUOTES, Sistema::apl()->charset);
    }
    
    /**
     * Esta función permite construir las opciones de un input
     * @param array $opciones
     * @return string
     */
    private static function crearOpcionesHtml($opciones = []){
        if($opciones === null){ return ""; }
        return implode(' ', array_map(
                    function($k, $v){ return "$k=\"$v\""; },
                    array_keys($opciones),
                    $opciones
                ));
    }
    
    /**
     * Esta función permite convertir un array de modelos en un array clave
     * valor, para así poderlo agregar como elementos de una lista
     * @param CModelo[] $modelos
     * @param string $valor
     * @param string $texto
     */
    public static function modeloLista($modelos, $valor, $texto){
        $elementos = [];
        foreach($modelos AS $modelo){
            $elementos[$modelo->$valor] = $modelo->$texto;
        }
        return $elementos;
    }
    
    /**
     * Esta función permite crear una etiqueta de imagen
     * @param string $src
     * @param array $opciones
     * @return string
     */
    public static function img($src, $opciones = []){
        $opciones['src'] = $src;
        return self::e('img', '', $opciones, false);
    }
    
}
