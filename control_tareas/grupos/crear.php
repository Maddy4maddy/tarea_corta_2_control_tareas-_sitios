<?php
require_once "../config/conexion.php";

$titulo_pagina = "Crear grupo";
$subtitulo_pagina = "Registro de un nuevo grupo de tareas";
$pagina_activa = "grupos";
$css_pagina = "grupos/crear.css";

$consultaTareas = $conexion->prepare("
    SELECT id_tarea, detalle, prioridad, fecha_limite
    FROM tareas
    WHERE estado = 'Pendiente'
    ORDER BY fecha_limite IS NULL, fecha_limite ASC, id_tarea DESC
");
$consultaTareas->execute();
$tareasPendientes = $consultaTareas->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $tareasSeleccionadas = isset($_POST["tareas"]) ? $_POST["tareas"] : [];

    if ($nombre === "") {
        $_SESSION["error"] = "Debe ingresar el nombre del grupo.";
    } else {
        $conexion->beginTransaction();

        try {
            $insertarGrupo = $conexion->prepare("
                INSERT INTO grupos (nombre)
                VALUES (:nombre)
            ");
            $insertarGrupo->bindParam(":nombre", $nombre);
            $insertarGrupo->execute();

            $id_grupo = $conexion->lastInsertId();

            if (!empty($tareasSeleccionadas)) {
                $actualizarTarea = $conexion->prepare("
                    UPDATE tareas
                    SET id_grupo = :id_grupo
                    WHERE id_tarea = :id_tarea
                    AND estado = 'Pendiente'
                ");

                foreach ($tareasSeleccionadas as $id_tarea) {
                    $actualizarTarea->bindParam(":id_grupo", $id_grupo);
                    $actualizarTarea->bindParam(":id_tarea", $id_tarea);
                    $actualizarTarea->execute();
                }
            }

            $conexion->commit();

            $_SESSION["mensaje"] = "El grupo fue creado correctamente.";
            header("Location: listar.php");
            exit;

        } catch (Exception $e) {
            $conexion->rollBack();
            $_SESSION["error"] = "Ocurrió un error al crear el grupo.";
        }
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="titulo-pagina">Nuevo grupo</h2>
        <p class="subtitulo-pagina">Ingrese el nombre del grupo y, si desea, seleccione tareas pendientes para asociarlas.</p>
    </div>

    <form method="POST" class="formulario-grupo">
        <div class="campo">
            <label for="nombre">Nombre del grupo</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="lista-tareas">
            <h3>Tareas pendientes para asociar al grupo</h3>

            <?php if (count($tareasPendientes) > 0): ?>
                <?php foreach ($tareasPendientes as $tarea): ?>
                    <div class="item-tarea">
                        <input type="checkbox" 
                               id="tarea_<?php echo $tarea["id_tarea"]; ?>" 
                               name="tareas[]" 
                               value="<?php echo $tarea["id_tarea"]; ?>">

                        <label for="tarea_<?php echo $tarea["id_tarea"]; ?>">
                            <strong><?php echo htmlspecialchars($tarea["detalle"]); ?></strong>
                            <br>
                            <span>
                                Prioridad: <?php echo htmlspecialchars($tarea["prioridad"]); ?> |
                                Fecha límite: <?php echo $tarea["fecha_limite"] ? htmlspecialchars($tarea["fecha_limite"]) : "Sin fecha"; ?>
                            </span>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sin-tareas">
                    No hay tareas pendientes disponibles para asociar.
                </div>
            <?php endif; ?>
        </div>

        <div class="botones-formulario">
            <button type="submit" class="btn btn-principal">Guardar grupo</button>
            <a href="listar.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>