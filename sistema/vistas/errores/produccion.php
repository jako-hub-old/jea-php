<?php 
    Sistema::apl()->mRecursos->registrarJQuery();
    Sistema::apl()->mRecursos->registrarBootstrap3();
    Sistema::apl()->mRecursos->registrarAwesomeFont();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php echo Sistema::apl()->charset; ?>" />
        <title><?php echo $this->titulo; ?></title>
        <style>
            .error-title{
                background: -moz-linear-gradient(left,  rgba(252,73,73,1) 0%, rgba(247,72,72,1) 2%, rgba(0,0,0,0) 100%); /* FF3.6-15 */
                background: -webkit-linear-gradient(left,  rgba(252,73,73,1) 0%,rgba(247,72,72,1) 2%,rgba(0,0,0,0) 100%); /* Chrome10-25,Safari5.1-6 */
                background: linear-gradient(to right,  rgba(252,73,73,1) 0%,rgba(247,72,72,1) 2%,rgba(0,0,0,0) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fc4949', endColorstr='#00000000',GradientType=1 ); /* IE6-9 */

                padding: 5px 40px;
                color: white;                
            }
            .icono, .error-title{
                text-shadow: 2px 2px 1px rgba(0, 0, 0, 0.38);
            }
            .icono{
                color: #FC4949;
                font-size: 100px;
            }
            .texto-error{
                font-size: 20px;
            }
            body{
                background-color: rgba(232, 231, 231, 0.22) !important;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="col-sm-offset-3 col-sm-6">
                
                <div class="page-header">
                    <h1 class="error-title">Error interno en el servidor</h1>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3 class="icono text-center"><?php echo CBoot::fa('exclamation-triangle'); ?></h3>                    
                        <p class="text-center texto-error">Upss, lo sentimos, ha ocurrido un error.</p>
                    </div>
                </div>
                
            </div>
        </div>
    </body>
</html>
