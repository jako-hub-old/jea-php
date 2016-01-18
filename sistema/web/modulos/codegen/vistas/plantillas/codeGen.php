<?php 
Sistema::apl()->mRecursos->registrarJQuery();
Sistema::apl()->mRecursos->registrarBootstrap3();
Sistema::apl()->mRecursos->registrarAwesomeFont();
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
            <h1>JEA-php CodeGen <?php echo CBoot::fa('code'); ?> </h1>
            <div class="row">
                <p class="col-sm-6">Bienvenido al generador de código, aquí puedes crear modelos, módulos o un crud completo para agilizar el 
                desarrollo de tu aplicación</p>                
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                <?php echo $this->contenido; ?>
                </div>
            </div>
        </div>
    </body>
</html>