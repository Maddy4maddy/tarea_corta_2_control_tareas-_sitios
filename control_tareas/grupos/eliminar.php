<?php
require_once "../config/conexion.php";

if (!isset($_GET["id"])) {
    $_SESSION["error"] = "No se indicó el grupo que desea eliminar.";
    header("Location: listar.php");
    exit;
}

$id_grupo = $_GET["id"];

$consulta = $conexion->prepare("
    SELECT id_grupo
    FROM grupos
    WHERE id_grupo = :id_grupo
");
$consulta->bindParam(":id_grupo", $id_grupo);
$consulta->execute();
$grupo = $consulta->fetch();

if (!$grupo) {
    $_SESSION["error"] = "El grupo seleccionado no existe.";
    header("Location: listar.php");
    exit;
}

$eliminar = $conexion->prepare("
    DELETE FROM grupos
    WHERE id_grupo = :id_grupo
");
$eliminar->bindParam(":id_grupo", $id_grupo);
$eliminar->execute();

$_SESSION["mensaje"] = "El grupo fue eliminado correctamente. Las tareas asociadas quedaron sin grupo.";
header("Location: listar.php");
exit;