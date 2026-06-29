<?php
session_start();
require_once "../config/conexion.php";

$titulo_pagina = "Tablero";
$subtitulo_pagina = "Vista Kanban";
$pagina_activa = "tablero";
$css_pagina = "tablero/tablero.css";

$estado = $_GET["estado"] ?? "";
$prioridad = $_GET["prioridad"] ?? "";
$responsable = $_GET["responsable"] ?? "";
$fecha = $_GET["fecha"] ?? "";

$sql = "SELECT t.*, r.nombre, r.apellidos
        FROM tareas t
        LEFT JOIN responsables r ON t.id_responsable = r.id_responsable
        WHERE 1=1";
$params = [];

if ($estado != "") {
    $sql .= " AND t.estado = :estado";
    $params["estado"] = $estado;
}
if ($prioridad != "") {
    $sql .= " AND t.prioridad = :prioridad";
    $params["prioridad"] = $prioridad;
}
if ($responsable != "") {
    $sql .= " AND t.id_responsable = :responsable";
    $params["responsable"] = $responsable;
}
if ($fecha != "") {
    $sql .= " AND t.fecha_limite = :fecha";
    $params["fecha"] = $fecha;
}
$sql .= " ORDER BY t.fecha_limite IS NULL, t.fecha_limite";

$q = $conexion->prepare($sql);
foreach ($params as $k => $v) {
    $q->bindValue(":" . $k, $v);
}
$q->execute();
$tareas = $q->fetchAll();

$cols = ["Pendiente" => [], "En progreso" => [], "Bloqueada" => [], "Finalizada" => []];
foreach ($tareas as $t) {
    $cols[$t["estado"]][] = $t;
}

$resps = $conexion->query("SELECT * FROM responsables ORDER BY nombre")->fetchAll();

include "../includes/header.php";
include "../includes/menu.php";
?>

<div class="contenido-principal">
    <div class="barra-superior">
        <h2><?php echo htmlspecialchars($titulo_pagina); ?></h2>
        <p><?php echo htmlspecialchars($subtitulo_pagina); ?></p>
    </div>

    <div class="contenedor">
        <div class="encabezado-listado">
            <div>
                <h2 class="titulo-pagina">Tablero Kanban</h2>
                <p class="subtitulo-pagina">Visualiza y gestiona las tareas por estado.</p>
            </div>
        </div>

        <form method="GET" class="filtros">
            <select name="estado">
                <option value="">Todos los estados</option>
                <?php foreach (array_keys($cols) as $e): ?>
                    <option <?php echo $estado == $e ? "selected" : ""; ?>><?php echo $e; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="prioridad">
                <option value="">Todas</option>
                <option <?php echo $prioridad == "Alta" ? "selected" : ""; ?>>Alta</option>
                <option <?php echo $prioridad == "Media" ? "selected" : ""; ?>>Media</option>
                <option <?php echo $prioridad == "Baja" ? "selected" : ""; ?>>Baja</option>
            </select>

            <select name="responsable">
                <option value="">Todos</option>
                <?php foreach ($resps as $r): ?>
                    <option value="<?php echo $r["id_responsable"]; ?>" <?php echo $responsable == $r["id_responsable"] ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($r["nombre"] . " " . $r["apellidos"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="fecha" value="<?php echo $fecha; ?>">

            <button class="btn btn-principal">Filtrar</button>
        </form>

        <div class="tablero">
            <?php foreach ($cols as $nombre => $lista): ?>
                <div class="columna">
                    <h3><?php echo $nombre; ?></h3>
                    <div class="lista-tareas">
                        <?php if (count($lista) > 0): ?>
                            <?php foreach ($lista as $t): ?>
                                <div class="tarjeta">
                                    <h4><?php echo htmlspecialchars($t["detalle"]); ?></h4>
                                    <p><strong>Prioridad:</strong> <?php echo $t["prioridad"]; ?></p>
                                    <p><strong>Responsable:</strong>
                                        <?php
                                        if ($t["nombre"]) {
                                            echo htmlspecialchars($t["nombre"] . " " . $t["apellidos"]);
                                        } else {
                                            echo '<span class="texto-suave">Sin responsable asignado</span>';
                                        }
                                        ?>
                                    </p>
                                    <p><strong>Fecha:</strong> <?php echo $t["fecha_limite"] ?: 'Sin fecha'; ?></p>
                                    <form action="actualizar_estado.php" method="POST">
                                        <input type="hidden" name="id_tarea" value="<?php echo $t["id_tarea"]; ?>">
                                        <select name="estado" onchange="this.form.submit()" class="form-control">
                                            <?php foreach (array_keys($cols) as $e): ?>
                                                <option value="<?php echo $e; ?>" <?php echo $t["estado"] == $e ? "selected" : ""; ?>>
                                                    <?php echo $e; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="mensaje-vacio">
                                <p>No hay tareas en este estado</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>