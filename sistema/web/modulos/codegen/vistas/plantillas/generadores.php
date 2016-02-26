<?php 
Sistema::apl()->mRecursos->registrarJQuery();
Sistema::apl()->mRecursos->registrarBootstrap3();
Sistema::apl()->mRecursos->registrarAwesomeFont();
Sistema::apl()->mRecursos->registrarSelect2();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php echo Sistema::apl()->charset; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $this->tituloPagina; ?></title>
        <style>
            body{
                background-color: #f5f5f5 !important;
            }
            .container{
                background-color: #FFF;
                min-height: 500px;
                border: 1px solid #eee;
            }
            .code-gen-header{
                background-color: rgba(48, 102, 254, 0.66);
                padding: 30px 9%;
                text-shadow: 0px 0px 5px rgba(0, 0, 0, 0.44);
                box-shadow: 0px 0px 9px 1px rgba(0,0,0,0.5);
                position: relative;
                z-index: 1000;
            }
            .code-gen-header h1, p{
                color: white;
            }
            .code-gen-header p{
                font-size: 18px;
            }
            .container{
                padding-top: 50px;
            }
        </style>
    </head>
    <body>
        <div class="code-gen-header">
            <h1>JEA-php <?php echo $this->titulo ?> </h1>
            <div class="row">
                <p class="col-sm-6"><?php echo $this->descripcion; ?></p>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-offset-1 col-sm-3">
                        <div class="panel panel-primary">
                            <div class="panel-heading text-center">
                                ¿Que deseas generar?
                            </div>
                            <div id="options" class="list-group">
                                <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/modelo'])?>" class="list-group-item"><?php echo CBoot::fa('database'); ?> Generar <b>Modelos</b></a>
                                <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/crud'])?>" class="list-group-item"><?php echo CBoot::fa('list-alt'); ?> Generar <b>CRUD</b></a>
                                <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/modulo'])?>" class="list-group-item"><?php echo CBoot::fa('cubes'); ?> Generar <b>Módulos</b></a>
                            </div>
                            <div class="panel-body">
                                <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/logout']) ?>" class="btn btn-block btn-danger">
                                    <?php echo CBoot::fa('sign-out'); ?> Salir
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="panel panel-primary">
                            <div class="panel-heading text-center">
                                Opciones
                            </div>
                            <div class="panel-body">
                            <?php echo $this->contenido; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(function(){
                jQuery("[data-select-two='true']").select2({
                    width: '100%'
                });
            });
        </script>
    </body>
</html>
