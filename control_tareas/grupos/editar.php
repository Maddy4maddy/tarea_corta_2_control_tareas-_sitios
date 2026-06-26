<?php
require_once "../config/conexion.php";

$titulo_pagina = "Editar grupo";
$subtitulo_pagina = "Modificar el nombre de un grupo";
$pagina_activa = "grupos";
$css_pagina = "grupos/editar.css";

if (!isset($_GET["id"])) {
    $_SESSION["error"] = "No se indicó el grupo que desea editar.";
    header("Location: listar.php");
    exit;
}

$id_grupo = $_GET["id"];

$consultaGrupo = $conexion->prepare("
    SELECT id_grupo, nombre
    FROM grupos
    WHERE id_grupo = :id_grupo
");
$consultaGrupo->bindParam(":id_grupo", $id_grupo);
$consultaGrupo->execute();
$grupo = $consultaGrupo->fetch();

if (!$grupo) {
    $_SESSION["error"] = "El grupo seleccionado no existe.";
    header("Location: listar.php");
    exit;
}

$consultaTotal = $conexion->prepare("
    SELECT COUNT(*) AS total
    FROM tareas
    WHERE id_grupo = :id_grupo
");
$consultaTotal->bindParam(":id_grupo", $id_grupo);
$consultaTotal->execute();
$total = $consultaTotal->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);

    if ($nombre === "") {
        $_SESSION["error"] = "Debe ingresar el nombre del grupo.";
    } else {
        $actualizar = $conexion->prepare("
            UPDATE grupos
            SET nombre = :nombre
            WHERE id_grupo = :id_grupo
        ");

        $actualizar->bindParam(":nombre", $nombre);
        $actualizar->bindParam(":id_grupo", $id_grupo);
        $actualizar->execute();

        $_SESSION["mensaje"] = "El grupo fue actualizado correctamente.";
        header("Location: listar.php");
        exit;
    }
}

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="form-card">
    <div class="form-header">
        <h2 class="titulo-pagina">Editar grupo</h2>
        <p class="subtitulo-pagina">Modifique el nombre del grupo seleccionado.</p>
    </div>

    <form method="POST" class="formulario-grupo">
        <div class="campo">
            <label for="nombre">Nombre del grupo</label>
            <input type="text" 
                   id="nombre" 
                   name="nombre" 
                   value="<?php echo htmlspecialchars($grupo["nombre"]); ?>" 
                   required>
        </div>

        <div class="info-grupo">
            Este grupo tiene actualmente <strong><?php echo $total["total"]; ?></strong> tarea(s) asociada(s).
        </div>

        <div class="botones-formulario">
            <button type="submit" class="btn btn-principal">Guardar cambios</button>
            <a href="listar.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>