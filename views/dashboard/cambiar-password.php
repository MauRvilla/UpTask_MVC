<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . "/../templates/alertas.php"; ?>
    
    <a href="/perfil" class="enlace">Volver</a>
    
    <form action="/cambiar-password" method="post" class="formulario">
        <div class="campo">
            <label for="password_actual">Password actual: </label>
            <input type="password" name="password_actual" 
            placeholder="Password actual">
        </div>
        
        <div class="campo">
            <label for="password">Nuevo Password: </label>
            <input type="password" name="password_nuevo" 
            placeholder="Tu nuevo password">
        </div>

        <input type="submit" value="Guardar cambios">
    </form>

</div>

<?php include_once __DIR__ . "/footer-dashboard.php"; ?>