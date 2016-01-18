<div class="row">
    <div class="col-sm-offset-1 col-sm-10">
        <div class=" text-center page-header">
            <h2>¿Que deseas generar?</h2>
        </div>
    </div>
</div>
<style>
    #opciones{
        text-align: center;
        padding: 0px;
        margin: 0px;
        margin-top: 20px;
    }
    
    #opciones li{
        list-style: none;
        display: inline-block;        
        margin: 0px 10px;
        transition: all 0.3s;
        border-radius: 100%;
        width: 150px;
        height: 150px;
        padding: 10px;
    }
    
    #opciones li i{
        color: rgba(48, 102, 254, 0.66);
        font-size: 80px
    }
    
    #opciones li p{
        color: black;
        margin-top: 10px;
        font-size: 18px;
    }
    #opciones li a:hover{
        text-decoration: none;
    }
    #opciones li:hover{
        /*text-shadow: 0px 3px 5px rgba(0,0,0,0.3);*/
        transform: translate(0px, -5px);
        border: 1px solid #7296FA;
        box-shadow: 0px 0px 10px #7296FA;
    }
    
</style>
<div class="row">
    <ul id="opciones">
        <li>
            <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/modelo']); ?>">
                <?php echo CBoot::fa('database'); ?>
                <p>Modelo</p>
            </a>
        </li>
        <li>
            <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/crud']); ?>">
                <?php echo CBoot::fa('list-alt'); ?>
                <p>CRUD</p>
            </a>
        </li>
        <li>
            <a href="<?php echo Sistema::apl()->crearUrl(['codegen/generador/modulo']); ?>">
                <?php echo CBoot::fa('cubes'); ?>
                <p>Módulo</p>
            </a>
        </li>
    </ul>
</div>