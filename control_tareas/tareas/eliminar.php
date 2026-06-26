<?php
require_once "../config/conexion.php";

if (!isset($_GET["id"])) {
    $_SESSION["error"] = "No se indicó la tarea que desea eliminar.";
    header("Location: listar.php");
    exit;
}

$id_tarea = $_GET["id"];

$consultaExiste = $conexion->prepare("SELECT id_tarea FROM tareas WHERE id_tarea = :id_tarea");
$consultaExiste->bindParam(":id_tarea", $id_tarea);
$consultaExiste->execute();
$tarea = $consultaExiste->fetch();

if (!$tarea) {
    $_SESSION["error"] = "La tarea seleccionada no existe.";
    header("Location: listar.php");
    exit;
}

$consulta = $conexion->prepare("DELETE FROM tareas WHERE id_tarea = :id_tarea");
$consulta->bindParam(":id_tarea", $id_tarea);
$consulta->execute();

$_SESSION["mensaje"] = "La tarea fue eliminada correctamente.";
header("Location: listar.php");
exit;