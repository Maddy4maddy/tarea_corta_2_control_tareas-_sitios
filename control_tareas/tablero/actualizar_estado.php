<?php
require_once "../config/conexion.php";

$id = $_POST["id_tarea"];
$estado = $_POST["estado"];

if ($estado == "Finalizada") {

    $q = $conexion->prepare("
        UPDATE tareas
        SET estado = :e,
            fecha_finalizacion = NOW()
        WHERE id_tarea = :id
    ");

} else {

    $q = $conexion->prepare("
        UPDATE tareas
        SET estado = :e,
            fecha_finalizacion = NULL
        WHERE id_tarea = :id
    ");

}

$q->execute([
    ":e" => $estado,
    ":id" => $id
]);

header("Location: tablero.php");
exit;