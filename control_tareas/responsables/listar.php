<?php
session_start();
require_once "../config/conexion.php";

$titulo_pagina = "Responsables";
$subtitulo_pagina = "Listado y control de responsables";
$pagina_activa = "responsables";
$css_pagina = "responsables/listar.css";

$sql = "SELECT * FROM responsables ORDER BY apellidos, nombre";
$consulta = $conexion->prepare($sql);
$consulta->execute();
$responsables = $consulta->fetchAll();

$total = count($responsables);

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
                <h2 class="titulo-pagina">Listado de responsables</h2>
                <p class="subtitulo-pagina">Administra los responsables para asignarlos a las tareas.</p>
            </div>

            <a href="crear.php" class="btn btn-principal">Nuevo responsable</a>
        </div>

        <div class="tarjetas-resumen">
            <div class="tarjeta-resumen">
                <span>Total responsables</span>
                <strong><?php echo $total; ?></strong>
            </div>
        </div>

        <div class="tabla-contenedor">
            <?php if (count($responsables) > 0): ?>
                <table class="tabla-tareas">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Identificacion</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($responsables as $responsable): ?>
                            <tr>
                                <td><?php echo $responsable["id_responsable"]; ?></td>
                                <td><?php echo htmlspecialchars($responsable["identificacion"]); ?></td>
                                <td><?php echo htmlspecialchars($responsable["nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($responsable["apellidos"]); ?></td>
                                <td>
                                    <div class="acciones">
                                        <a href="editar.php?id=<?php echo $responsable["id_responsable"]; ?>" class="btn btn-editar">
                                            Editar
                                        </a>
                                        <a href="eliminar.php?id=<?php echo $responsable["id_responsable"]; ?>" 
                                           class="btn btn-eliminar"
                                           onclick="return confirm('¿Desea eliminar este responsable?');">
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
                    <h3>No hay responsables registrados</h3>
                    <p>Puede crear un nuevo responsable desde el boton superior.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>