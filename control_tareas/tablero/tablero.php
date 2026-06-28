<?php
require_once "../config/conexion.php";
$titulo_pagina="Tablero";
$subtitulo_pagina="Vista Kanban";
$pagina_activa="tablero";
$css_pagina="tablero/tablero.css";

$estado=$_GET["estado"]??"";
$prioridad=$_GET["prioridad"]??"";
$responsable=$_GET["responsable"]??"";
$fecha=$_GET["fecha"]??"";

$sql="SELECT t.*,r.nombre,r.apellidos
FROM tareas t
LEFT JOIN responsables r ON t.id_responsable=r.id_responsable
WHERE 1=1";
$params=[];

if($estado!=""){ $sql.=" AND t.estado=:estado"; $params["estado"]=$estado; }
if($prioridad!=""){ $sql.=" AND t.prioridad=:prioridad"; $params["prioridad"]=$prioridad; }
if($responsable!=""){ $sql.=" AND t.id_responsable=:responsable"; $params["responsable"]=$responsable; }
if($fecha!=""){ $sql.=" AND t.fecha_limite=:fecha"; $params["fecha"]=$fecha; }
$sql.=" ORDER BY t.fecha_limite IS NULL,t.fecha_limite";

$q=$conexion->prepare($sql);
foreach($params as $k=>$v){ $q->bindValue(":".$k,$v); }
$q->execute();
$tareas=$q->fetchAll();

$cols=["Pendiente"=>[],"En progreso"=>[],"Bloqueada"=>[],"Finalizada"=>[]];
foreach($tareas as $t){ $cols[$t["estado"]][]=$t; }

$resps=$conexion->query("SELECT * FROM responsables ORDER BY nombre")->fetchAll();

include "../includes/header.php";
include "../includes/menu.php";
?>
<form method="GET" class="filtros">
<select name="estado"><option value="">Todos los estados</option><?php foreach(array_keys($cols) as $e){?><option <?=$estado==$e?"selected":""?>><?=$e?></option><?php }?></select>
<select name="prioridad"><option value="">Todas</option><option <?=$prioridad=="Alta"?"selected":""?>>Alta</option><option <?=$prioridad=="Media"?"selected":""?>>Media</option><option <?=$prioridad=="Baja"?"selected":""?>>Baja</option></select>
<select name="responsable"><option value="">Todos</option><?php foreach($resps as $r){?><option value="<?=$r["id_responsable"]?>" <?=$responsable==$r["id_responsable"]?"selected":""?>><?=htmlspecialchars($r["nombre"]." ".$r["apellidos"])?></option><?php }?></select>
<input type="date" name="fecha" value="<?=$fecha?>">
<button>Filtrar</button>
</form>

<div class="tablero">
<?php foreach($cols as $nombre=>$lista){ ?>
<div class="columna">
<h3><?=$nombre?></h3>
<?php foreach($lista as $t){ ?>
<div class="tarjeta">
<strong><?=htmlspecialchars($t["detalle"])?></strong><br>
Prioridad: <?=$t["prioridad"]?><br>
Responsable:
<?= $t["nombre"]?htmlspecialchars($t["nombre"]." ".$t["apellidos"]):"Sin responsable asignado";?><br>
Fecha: <?=$t["fecha_limite"]?:'Sin fecha';?>
<form action="actualizar_estado.php" method="POST">
<input type="hidden" name="id_tarea" value="<?=$t["id_tarea"]?>">
<select name="estado" onchange="this.form.submit()">
<?php foreach(array_keys($cols) as $e){?><option value="<?=$e?>" <?=$t["estado"]==$e?"selected":""?>><?=$e?></option><?php }?>
</select>
</form>
</div>
<?php } ?>
</div>
<?php } ?>
</div>
<?php include "../includes/footer.php"; ?>
