<?php 
    Sistema::apl()->mRecursos->registrarJQuery();
    Sistema::apl()->mRecursos->registrarBootstrap3();
    $estilos = ".titulo-error{ color:red;}" .
            ".archivo-con-error{ font-weight: 600; }" .
            ".linea-codigo{" .
                "border-right: 1px solid black;".
                "padding-right: 10px;" .
                "margin-right: 20px;" .
            "}".
            ".linea-con-error{" .
                "background-color: #F9C8C8;" .
                "width: 100%;" .
                "display: inline-block;" . 
            "}" .
            ".rastreo h4{" .
                "cursor: pointer;" .
                "border-radius: 2px;" .
                "transition: all 0.2s;" .
            "}" .
            ".rastreo h4:hover{" .
                "background-color: rgba(128, 0, 0, 0.2);" .
                "padding: 4px;" .
            "}";
    Sistema::apl()->mRecursos->registrarEstilosCliente($estilos);
    $script = 'jQuery(".rastreo h4").click(function(){'.
                        'jQuery(this).parent().find(".archivo-rastreo").slideToggle();'.
                    '});';
    Sistema::apl()->mRecursos->registrarScriptCliente($script, CMRecursos::POS_READY);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="<?php echo Sistema::apl()->charset; ?>" />
        <title><?php echo $this->titulo; ?></title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="page-header">
                    <h1 class="titulo-error"><?php echo $this->titulo ?></h1>
                </div>
                <div class="well">
                    <?php echo $this->mensaje; ?>
                </div>
                <p class="archivo-con-error"><?php echo $this->archivo; ?> <span class="badge"><?php echo $this->linea; ?></span></p>
                <?php 
                echo $this->verError($this->archivo, $this->linea);
                ?>
                <h3 class="titulo-rastreo">Rastreo del error</h3>
                <?php foreach($this->rastreo AS $rastro): ?>
                <div class="rastreo">
                    <h4>#<?php echo ++ $conteo; ?> <small><?php echo $rastro['file']; ?></small></h4>
                    <div class="archivo-rastreo" style="display: none;">
                        <?php echo $this->verError($rastro['file'], $rastro['line']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
