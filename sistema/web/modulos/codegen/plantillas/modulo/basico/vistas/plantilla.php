<?php echo "<?php\n"; ?>
Sistema::apl()->mRecursos->registrarJQuery();
Sistema::apl()->mRecursos->registrarBootstrap3();
Sistema::apl()->mRecursos->registrarAwesomeFont();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php echo "<?php echo Sistema::apl()->charset; ?>"; ?>">
        <title><?php echo "<?php echo \$this->tituloPagina; ?>"; ?></title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">

                    <div class="jumbotron">
                        <h1>Módulo <?php echo $nMod; ?></h1>
                    </div>
                    <?php echo "<?php echo \$this->contenido; ?>"; ?>
                </div>
            </div>    	
        </div>
    </body>
</html>
