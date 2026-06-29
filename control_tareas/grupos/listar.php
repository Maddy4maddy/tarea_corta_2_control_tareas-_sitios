<?php
session_start();
require_once "../config/conexion.php";

$titulo_pagina = "Grupos";
$subtitulo_pagina = "Listado de grupos de tareas";
$pagina_activa = "grupos";
$css_pagina = "grupos/listar.css";

$sql = "
    SELECT 
        g.id_grupo,
        g.nombre,
        COUNT(t.id_tarea) AS total_tareas
    FROM grupos g
    LEFT JOIN tareas t ON g.id_grupo = t.id_grupo
    GROUP BY g.id_grupo, g.nombre
    ORDER BY g.nombre ASC
";

$consulta = $conexion->prepare($sql);
$consulta->execute();
$grupos = $consulta->fetchAll();

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="contenido-principal">
    <div class="barra-superior">
        <h2><?php echo htmlspecialchars($titulo_pagina); ?></h2>
        <p><?php echo htmlspecialchars($subtitulo_pagina); ?></p>
    </div>

    <div class="contenedor">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alerta alerta-<?php echo $_SESSION['tipo']; ?>">
                <?php 
                echo htmlspecialchars($_SESSION['mensaje']);
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo']);
                ?>
            </div>
        <?php endif; ?>

        <div class="encabezado-listado">
            <div>
                <h2 class="titulo-pagina">Listado de grupos</h2>
                <p class="subtitulo-pagina">Administra los grupos y revisa las tareas asociadas.</p>
            </div>

            <a href="crear.php" class="btn btn-principal">Nuevo grupo</a>
        </div>

        <div class="tabla-contenedor">
            <?php if (count($grupos) > 0): ?>
                <table class="tabla-tareas">
                    <thead>
                        <tr>
                            <th>Nombre del grupo</th>
                            <th>Total de tareas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($grupos as $grupo): ?>
                            <tr>
                                <td>
                                    <span class="detalle-tarea">
                                        <?php echo htmlspecialchars($grupo["nombre"]); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="total-tareas">
                                        <?php echo $grupo["total_tareas"]; ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="acciones">
                                        <a href="ver_tareas.php?id=<?php echo $grupo["id_grupo"]; ?>" class="btn btn-ver">
                                            Ver tareas
                                        </a>

                                        <a href="editar.php?id=<?php echo $grupo["id_grupo"]; ?>" class="btn btn-editar">
                                            Editar
                                        </a>

                                        <a href="eliminar.php?id=<?php echo $grupo["id_grupo"]; ?>" 
                                           class="btn btn-eliminar"
                                           onclick="return confirm('¿Desea eliminar este grupo? Las tareas quedaran sin grupo.');">
                                            Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="mensaje-vacio">
                    <h3>No hay grupos registrados</h3>
                    <p>Puede crear un nuevo grupo desde el boton superior.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>