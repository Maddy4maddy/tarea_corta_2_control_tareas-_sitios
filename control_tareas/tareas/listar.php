<?php
require_once "../config/conexion.php";

$titulo_pagina = "Tareas";
$subtitulo_pagina = "Listado y control de tareas";
$pagina_activa = "tareas";
$css_pagina = "tareas/listar.css";

$sql = "
    SELECT 
        t.id_tarea,
        t.detalle,
        t.prioridad,
        t.fecha_limite,
        t.estado,
        t.fecha_finalizacion,
        t.id_responsable,
        t.id_grupo,
        r.nombre AS nombre_responsable,
        r.apellidos AS apellidos_responsable,
        g.nombre AS nombre_grupo
    FROM tareas t
    LEFT JOIN responsables r ON t.id_responsable = r.id_responsable
    LEFT JOIN grupos g ON t.id_grupo = g.id_grupo
    ORDER BY 
        CASE WHEN t.estado = 'Finalizada' THEN 1 ELSE 0 END,
        t.fecha_limite IS NULL,
        t.fecha_limite ASC,
        t.id_tarea DESC
";

$consulta = $conexion->prepare($sql);
$consulta->execute();
$tareas = $consulta->fetchAll();

$total = count($tareas);
$pendientes = 0;
$progreso = 0;
$bloqueadas = 0;
$finalizadas = 0;

foreach ($tareas as $tarea) {
    if ($tarea["estado"] === "Pendiente") {
        $pendientes++;
    } elseif ($tarea["estado"] === "En progreso") {
        $progreso++;
    } elseif ($tarea["estado"] === "Bloqueada") {
        $bloqueadas++;
    } elseif ($tarea["estado"] === "Finalizada") {
        $finalizadas++;
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="encabezado-listado">
    <div>
        <h2 class="titulo-pagina">Listado de tareas</h2>
        <p class="subtitulo-pagina">Administra las tareas, responsables, grupos, estados y fechas.</p>
    </div>

    <a href="crear.php" class="btn btn-principal">Nueva tarea</a>
</div>

<div class="tarjetas-resumen">
    <div class="tarjeta-resumen">
        <span>Total</span>
        <strong><?php echo $total; ?></strong>
    </div>

    <div class="tarjeta-resumen">
        <span>Pendientes</span>
        <strong><?php echo $pendientes; ?></strong>
    </div>

    <div class="tarjeta-resumen tarjeta-rosada">
        <span>En progreso</span>
        <strong><?php echo $progreso; ?></strong>
    </div>

    <div class="tarjeta-resumen tarjeta-oscura">
        <span>Finalizadas</span>
        <strong><?php echo $finalizadas; ?></strong>
    </div>
</div>

<div class="tabla-contenedor">
    <?php if (count($tareas) > 0): ?>
        <table class="tabla-tareas">
            <thead>
                <tr>
                    <th>Detalle</th>
                    <th>Responsable</th>
                    <th>Grupo</th>
                    <th>Prioridad</th>
                    <th>Fecha límite</th>
                    <th>Estado</th>
                    <th>Fecha finalización</th>
                    <th>Cambiar estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tareas as $tarea): ?>
                    <?php
                        $claseEstado = strtolower(str_replace(" ", "-", $tarea["estado"]));
                        $clasePrioridad = strtolower($tarea["prioridad"]);

                        $responsable = "Sin responsable asignado";

                        if (!empty($tarea["nombre_responsable"])) {
                            $responsable = $tarea["nombre_responsable"] . " " . $tarea["apellidos_responsable"];
                        }

                        $grupo = !empty($tarea["nombre_grupo"]) ? $tarea["nombre_grupo"] : "Sin grupo";
                    ?>

                    <tr>
                        <td>
                            <span class="detalle-tarea <?php echo $tarea["estado"] === "Finalizada" ? "tarea-finalizada" : ""; ?>">
                                <?php echo htmlspecialchars($tarea["detalle"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($responsable === "Sin responsable asignado"): ?>
                                <span class="texto-suave">Sin responsable asignado</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($responsable); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($grupo === "Sin grupo"): ?>
                                <span class="texto-suave">Sin grupo</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($grupo); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="prioridad prioridad-<?php echo $clasePrioridad; ?>">
                                <?php echo htmlspecialchars($tarea["prioridad"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo $tarea["fecha_limite"] ? htmlspecialchars($tarea["fecha_limite"]) : "Sin fecha"; ?>
                        </td>

                        <td>
                            <span class="estado estado-<?php echo $claseEstado; ?>">
                                <?php echo htmlspecialchars($tarea["estado"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo $tarea["fecha_finalizacion"] ? htmlspecialchars($tarea["fecha_finalizacion"]) : "Sin finalizar"; ?>
                        </td>

                        <td>
                            <?php if ($tarea["estado"] !== "Finalizada"): ?>
                                <form action="cambiar_estado.php" method="POST" class="form-estado">
                                    <input type="hidden" name="id_tarea" value="<?php echo $tarea["id_tarea"]; ?>">

                                    <select name="nuevo_estado" required>
                                        <option value="">Seleccione</option>

                                        <?php if ($tarea["estado"] === "Pendiente"): ?>
                                            <option value="En progreso">En progreso</option>
                                        <?php endif; ?>

                                        <?php if ($tarea["estado"] === "En progreso"): ?>
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="Bloqueada">Bloqueada</option>
                                            <option value="Finalizada">Finalizada</option>
                                        <?php endif; ?>

                                        <?php if ($tarea["estado"] === "Bloqueada"): ?>
                                            <option value="En progreso">En progreso</option>
                                        <?php endif; ?>
                                    </select>

                                    <button type="submit" class="btn btn-cambiar">Cambiar</button>
                                </form>
                            <?php else: ?>
                                <span class="texto-suave">Finalizada</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="acciones">
                                <a href="editar.php?id=<?php echo $tarea["id_tarea"]; ?>" class="btn btn-editar">
                                    Editar
                                </a>

                                <?php if ($tarea["estado"] === "En progreso"): ?>
                                    <a href="finalizar.php?id=<?php echo $tarea["id_tarea"]; ?>" class="btn btn-finalizar">
                                        Finalizar
                                    </a>
                                <?php elseif ($tarea["estado"] === "Finalizada"): ?>
                                    <a href="reactivar.php?id=<?php echo $tarea["id_tarea"]; ?>" class="btn btn-reactivar">
                                        Reactivar
                                    </a>
                                <?php endif; ?>

                                <a href="eliminar.php?id=<?php echo $tarea["id_tarea"]; ?>" 
                                   class="btn btn-eliminar"
                                   onclick="return confirm('¿Desea eliminar esta tarea?');">
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
            <h3>No hay tareas registradas</h3>
            <p>Puede crear una nueva tarea desde el botón superior.</p>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>