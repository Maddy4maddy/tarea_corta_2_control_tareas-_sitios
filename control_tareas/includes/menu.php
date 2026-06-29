<aside class="menu-lateral">
    <div class="logo">
        <div class="logo-icono">✓</div>
        <h1>Control de Tareas</h1>
        <p>Gestión personal</p>
    </div>

     <nav class="menu">
     <a href="<?php echo $base_url; ?>/responsables/listar.php"
           class="<?php echo $pagina_activa === 'responsables' ? 'activo' : ''; ?>">
            Responsables
        </a>

        <a href="<?php echo $base_url; ?>/tareas/listar.php"
           class="<?php echo $pagina_activa === 'tareas' ? 'activo' : ''; ?>">
            Tareas
        </a>

        <a href="<?php echo $base_url; ?>/grupos/listar.php"
           class="<?php echo $pagina_activa === 'grupos' ? 'activo' : ''; ?>">
            Grupos
        </a>

        <a href="<?php echo $base_url; ?>/tablero/tablero.php"
            class="<?php echo $pagina_activa === 'tablero' ? 'activo' : ''; ?>">
            Tablero
        </a>
    </nav>
</aside>