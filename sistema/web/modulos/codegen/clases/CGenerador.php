<?php
/**
 * Esta clase es el asistente para generar código
 * @package sistema.web.modulos.codegen
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @version 1.0.0
 * @copyright (c) 2016, jako
 */
class CGenerador {
    /**
     * Clase encargada de examinar la base de datos
     * @var CEsquema 
     */
    private $esquema;
    
    public function __construct(CEsquema $esquema) {
        $this->esquema = $esquema;
    }
    
    /**
     * Esta función se encarga de generar un modelo
     * @param string $tabla
     * @return boolean
     */
    public function generarModelo($tabla, $plantilla = 'basico', $paraCrud = false){
        $atributos = $this->esquema->obtenerAtributos($tabla);        
        $relaciones = $this->esquema->obtenerRelaciones($tabla);
        $nTabla = str_replace($this->esquema->getPrefijo(), '', $tabla);
        $nClase = str_replace(' ', '', ucwords(str_replace('_', ' ', $nTabla)));
        
        $rutaPlantilla = !$paraCrud? 
                Sistema::resolverRuta("!web.modulos.codegen.plantillas.modelo.$plantilla", true) : 
                Sistema::resolverRuta("!web.modulos.codegen.plantillas.crud.$plantilla.modelo", true);
        
        $strPlantilla = $this->cargarPlantilla(
                $rutaPlantilla, [
                'atributos' => $atributos,
                'relaciones' => $relaciones,
                'tabla' => $tabla,
                'nTabla' => $nTabla,
                'nClase' => $nClase,
        ]);
        $rutaModelos = Sistema::resolverRuta("!aplicacion.modelos");        
        
        return $this->guardarArchivo($rutaModelos, $nClase, $strPlantilla);
    }
    
    
    /***********************************************
     ***********      Funciones MODULO      ********
     ***********************************************/
    
    public function generarModulo($nombre, $plantilla = "basico"){
        $c = [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 
        ];
        # limipamos el nombre y construimos el nombre de la carpeta y el de la clase
        $nFiltrado = strtr($nombre, $c);
        $nCarpeta = str_replace(' ', '_', $nFiltrado);
        $nClase = str_replace(' ', '', ucwords($nFiltrado));
        # construimos la ruta del módulo
        $ruta = Sistema::resolverRuta("!modulos.$nCarpeta");
        # creamos la carpeta si no existe
        if(!is_dir($ruta)){ mkdir($ruta); }
        
        # creamos la ruta de las plantillas
        $rPlantilla = Sistema::resolverRuta("!web.modulos.codegen.plantillas.modulo.$plantilla");
        
        $ctrl = $this->crearControladorMod($nClase, $rPlantilla, $ruta.DS.'controladores');
        $vista = $this->generarVistaMod($nombre, $rPlantilla, $ruta.DS.'vistas');
        $modulo = $this->crearArchivoMod($nClase, $ruta, $rPlantilla);
        
        return [
            'error' => $ctrl && $vista && $modulo,
            'ruta' => "!modulos.$nCarpeta",
            'clase' => $nClase,
            'ctrl' => 'principal',
        ];
    }
    
    private function crearControladorMod($clase, $rPlantilla, $rDes){
        if(!is_dir($rDes)){ mkdir($rDes); }
        if(!file_exists($rPlantilla.DS."controlador.php")){
            throw new CExAplicacion("La ruta para las plantillas no es valida");
        }
        $contenido = $this->cargarPlantilla($rPlantilla . DS . 'controlador.php');
        return $this->guardarArchivo($rDes, 'CtrlPrincipal', $contenido);
    }
    
    private function generarVistaMod($nombre, $rPlantillas, $rDestino) {
        # creamos el directorio de vistas si no existe
        if(!is_dir($rDestino)){ mkdir($rDestino); }
        # creamos el directorio de plantillas si no existe
        if(!is_dir($rDestino.DS.'plantillas')){ mkdir($rDestino.DS.'plantillas'); }
        # creamos el directorio del controlador principal si no existe
        if(!is_dir($rDestino.DS.'principal')){ mkdir($rDestino.DS.'principal'); }
       
        if(!file_exists($rPlantillas.DS.'vistas'.DS.'plantilla.php') || 
           !file_exists($rPlantillas.DS.'vistas'.DS.'vista.php')){
            throw new CExAplicacion("Falta una de las plantillas para vista [plantilla.php | vista.php]");
        }
        
        # cargamos la plantilla y la vista
        $cPlantilla = $this->cargarPlantilla($rPlantillas.DS.'vistas'.DS.'plantilla.php', [
            'nMod' => ucfirst($nombre),
        ]);        
        $cVista = $this->cargarPlantilla($rPlantillas.DS.'vistas'.DS.'vista.php', [
            'ubicacion' => str_replace(DS.'vistas', '', $rDestino),
        ]);
        
        # guardamos la plantilla y la vista
        $plantilla = $this->guardarArchivo($rDestino.DS.'plantillas', 'intModulo', $cPlantilla);
        $vista = $this->guardarArchivo($rDestino.DS.'principal', 'inicio', $cVista);
        
        return $plantilla && $vista;
    }
    
    private function crearArchivoMod($clase, $rutaDes, $rPlantilla){
        if(!file_exists($rPlantilla.DS.'modulo.php')){
            throw new CExAplicacion("No existe el archivo base para el módulo");
        }
        $cModulo = $this->cargarPlantilla($rPlantilla.DS.'modulo.php', [
            'nClase' => $clase,
        ]);
        return $this->guardarArchivo($rutaDes, $clase, $cModulo);
    }
    
    /***********************************************
     ***********      Funciones CRUD        ********
     ***********************************************/    
    
    /**
     * Esta función retorna los archivos de una plantilla para crud
     * @param string $plantilla
     * @return array
     * @throws CExAplicacion
     */
    public function obtenerArchivosCrud($plantilla = "basica"){
        $rutaPlantillas = Sistema::resolverRuta("!web.modulos.codegen.plantillas.crud.$plantilla");
        if(!file_exists($rutaPlantillas) || !is_dir($rutaPlantillas)){
            throw new CExAplicacion("La ruta de la plantilla no es valida");
        }
        $directorios = scandir($rutaPlantillas);
        # removemos las dos primeras posiciones que son [.] y [..]
        unset($directorios[0], $directorios[1]);
        return $directorios;
    }    
    
    /**
     * Esta función detona el proceso de generación de un crud
     * @param string $tabla
     * @param array $archivos
     * @param string $plantilla
     * @param string $autor
     * @return boolean
     */
    public function generarCrud($tabla, $archivos = [], $plantilla = 'basica', $autor = ''){
        $rutaPlantilla = Sistema::resolverRuta("!web.modulos.codegen.plantillas.crud.$plantilla");
        $proyectoDes = Sistema::resolverRuta("!aplicacion");
        # generamos el controlador en caso de que exista
        $modelo = $this->generarModeloCrud($archivos, $tabla, $plantilla);        
        # generamos el nombre de la tabla sin prefijo
        $pre = $this->esquema->getPrefijo();
        # 1. removemos prefijo, 2. comvertismo _ en ' ', 3. aplicamos cammelCase
        # 4. removemos espacios
        $nTabla = str_replace(' ', '', ucwords(str_replace('_', ' ', str_replace($pre, '', $tabla))));
        
        $ctrl = $this->generarControlador($nTabla, $archivos, $rutaPlantilla, $proyectoDes.DS.'controladores', $autor);
        
        $carpetaCtrl = lcfirst($nTabla);
        $desVistas = $proyectoDes.DS.'vistas'.DS.$carpetaCtrl;
        #cramos la carpeta del controlador
        if(!is_dir($desVistas)){
            mkdir($desVistas);
        }
        
        # generamos las vistas
        $vistas = $this->generarVistas($nTabla, $archivos, $rutaPlantilla, $desVistas);
        
        return $ctrl && $modelo && $vistas;
        
    }
        
    /**
     * Esta función genera un modelo usando una plantilla para crud
     * @param array $archivos
     * @param string $tabla
     * @param string $plantilla
     * @return boolean
     */
    private function generarModeloCrud(&$archivos, $tabla, $plantilla){
        $indiceModelo = array_search('modelo.php', $archivos);
        if($indiceModelo === false){ return true; }
        unset($archivos[$indiceModelo]);
        return $this->generarModelo($tabla, $plantilla, true);
    }
    
    /**
     * Esta función genera un controlador usando una plantilla seleccionada
     * @param string $tabla
     * @param array $archivos
     * @param string $rutaPlantilla
     * @param string $rutaDes
     * @param string $autor
     * @return boolean
     * @throws CExAplicacion Si no existe la plantilla
     */
    private function generarControlador($tabla, &$archivos, $rutaPlantilla, $rutaDes, $autor){
        $indiceCtrl = array_search('controlador.php', $archivos);
        
        if($indiceCtrl === false){ return true; }
        
        $rutaControlador = $rutaPlantilla.DS."controlador.php";
        
        if(!file_exists($rutaControlador)){
            throw new CExAplicacion("No existe la plantilla para el controlador");
        }
        
        $nTabla = ucfirst($tabla);
        $strPlantilla = $this->cargarPlantilla($rutaControlador, [
            'nTabla' => $nTabla,
            'autor' => $autor,
        ]);
        
        unset($archivos[$indiceCtrl]);
        
        return $this->guardarArchivo($rutaDes, "Ctrl" . $nTabla, $strPlantilla);
    }
    
    /**
     * Esta función genera las vistas para un crud
     * @param string $tabla
     * @param array $archivos
     * @param string $rutaPlantilla
     * @param string $rutaDestino
     * @return boolean
     */
    private function generarVistas($tabla, $archivos, $rutaPlantilla, $rutaDestino){
        $modelo = new $tabla();
        $error = false;
        
        if(count($archivos) == 0){ return true; }
        
        foreach ($archivos AS $archivo){
            $ruta = $rutaPlantilla.DS.$archivo;
            if(!file_exists($ruta) && !is_file($ruta)){
                $error = true;
                break;
            }
            $contenido = $this->cargarPlantilla($ruta, ['nTabla' => $tabla, 'modelo' => $modelo]);
            $this->guardarArchivo($rutaDestino, str_replace('.php', '', $archivo), $contenido);
        }
        
        return !$error;
    }    
    
    /**
     * Esta función se encarga de cargar una plantilla
     * @param string $ruta
     * @param array $opciones
     * @return string
     * @throws CExAplicacion
     */
    private function cargarPlantilla($ruta, $opciones = []){
        if(!file_exists($ruta)){
            throw new CExAplicacion("No existe la plantilla seleccionada $ruta");
        }
        ob_start();
        foreach($opciones AS $nombre=>$valor){ $$nombre = $valor; }
        include $ruta;
        return ob_get_clean();
    }
    
    /**
     * Esta función sirve para traducir el tipo de campo de mysql al tipo manejado en php
     * @param string $tipo
     * @param boolean $filtrado
     * @return string
     */
    private function obtenerTipo($tipo, $filtrado = true){
        $partes = explode('(', $tipo);
        $t = $partes[0];
        if(!$filtrado) { return $t; }
        switch ($t){
            case 'varchar' :
            case 'text' :
            case 'date': 
                return 'string';
            default : return $t;
        }
    }
    
    /**
     * Esta función ayuda a construir el nombre de la relación
     * @param string $nombre
     * @return string
     */
    private function nombreRelacion($nombre){
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $nombre)));
    }
    
    /**
     * Esta función se encarga de guardar un archivo en la ruta especificada
     * @param string $ruta
     * @param string $nombre
     * @param string $contenido
     * @param string $modo
     * @return boolean
     * @throws CExAplicacion
     */
    private function guardarArchivo($ruta, $nombre, $contenido, $modo = 'w'){
        if(!file_exists($ruta) || !is_dir($ruta)){
            throw new CExAplicacion("No existe la ruta para guardar el archivo");
        }
        $handler = fopen($ruta.DS."$nombre.php", $modo);
        fwrite($handler, $contenido);
        return fclose($handler);
    }
}