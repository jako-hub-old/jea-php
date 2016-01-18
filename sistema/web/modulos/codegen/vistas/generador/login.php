<div class="col-sm-offset-3 col-sm-6">
    <form id="log-form" method="POST">
        <div id="admin-login-panel" class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="text-center">Iniciar sesión</h3>
            </div>
            <div class="panel-body">
                <div id="error-log"></div>
                    <?php
                    if($error){
                        echo CBoot::alert("Usuario o contraseña incorrectos", "danger", [], true);
                    }
                    ?>
                    <?php echo CBoot::textAddOn('', [
                        'id' => 'user',
                        'placeholder' => 'Nombre de usuario',
                        'name' => 'log[username]',
                        'pre' => CBoot::fa('user'),
                        'group' => true,
                        'autofocus' => true,
                    ])?>
                    <?php echo CBoot::passwordAddOn('', [
                        'id' => 'pass',
                        'placeholder' => 'Contraseña',
                        'name' => 'log[password]',
                        'pre' => CBoot::fa('unlock-alt'),
                        'group' => true,
                    ])?>

            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-7">
                        <!--remember me <input type="checkbox">-->
                    </div>
                    <div class="col-sm-5 text-right">
                        <?php echo CBoot::botonS("Iniciar sesión", ['class' => 'btn-block']); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>