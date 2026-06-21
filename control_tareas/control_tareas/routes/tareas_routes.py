from flask import Blueprint, render_template, request, redirect, url_for, flash
from models.tarea_model import TareaModel

tareas_bp = Blueprint("tareas", __name__, url_prefix="/tareas")


@tareas_bp.route("/")
def listar_tareas():
    tareas = TareaModel.obtener_todas()
    return render_template("tareas/listar.html", tareas=tareas)



@tareas_bp.route("/tablero")
def tablero():

    tareas = TareaModel.obtener_todas()

    return render_template(
        "tareas/tablero.html",
        tareas=tareas
    )


@tareas_bp.route("/crear", methods=["GET", "POST"])
def crear_tarea():
    responsables = TareaModel.obtener_responsables()
    grupos = TareaModel.obtener_grupos()

    if request.method == "POST":
        detalle = request.form["detalle"]
        prioridad = request.form["prioridad"]
        fecha_limite = request.form["fecha_limite"]
        id_responsable = request.form["id_responsable"]
        id_grupo = request.form["id_grupo"]

        if fecha_limite == "":
            fecha_limite = None

        if id_responsable == "":
            id_responsable = None

        if id_grupo == "":
            id_grupo = None

        TareaModel.insertar(
            detalle,
            prioridad,
            fecha_limite,
            id_responsable,
            id_grupo
        )

        flash("Tarea creada correctamente.")
        return redirect(url_for("tareas.listar_tareas"))

    return render_template(
        "tareas/crear.html",
        responsables=responsables,
        grupos=grupos
    )


@tareas_bp.route("/editar/<int:id_tarea>", methods=["GET", "POST"])
def editar_tarea(id_tarea):
    tarea = TareaModel.obtener_por_id(id_tarea)
    responsables = TareaModel.obtener_responsables()
    grupos = TareaModel.obtener_grupos()

    if request.method == "POST":
        detalle = request.form["detalle"]
        prioridad = request.form["prioridad"]
        fecha_limite = request.form["fecha_limite"]
        id_responsable = request.form["id_responsable"]
        id_grupo = request.form["id_grupo"]

        if fecha_limite == "":
            fecha_limite = None

        if id_responsable == "":
            id_responsable = None

        if id_grupo == "":
            id_grupo = None

        TareaModel.actualizar(
            id_tarea,
            detalle,
            prioridad,
            fecha_limite,
            id_responsable,
            id_grupo
        )

        flash("Tarea actualizada correctamente.")
        return redirect(url_for("tareas.listar_tareas"))

    return render_template(
        "tareas/editar.html",
        tarea=tarea,
        responsables=responsables,
        grupos=grupos
    )


@tareas_bp.route("/eliminar/<int:id_tarea>")
def eliminar_tarea(id_tarea):
    TareaModel.eliminar(id_tarea)
    flash("Tarea eliminada correctamente.")
    return redirect(url_for("tareas.listar_tareas"))


@tareas_bp.route("/finalizar/<int:id_tarea>")
def finalizar_tarea(id_tarea):
    tarea = TareaModel.obtener_por_id(id_tarea)

    if tarea["estado"] == "En progreso":
        TareaModel.finalizar(id_tarea)
        flash("Tarea finalizada correctamente.")
    else:
        flash("Solo se puede finalizar una tarea que esté en progreso.")

    return redirect(url_for("tareas.listar_tareas"))


@tareas_bp.route("/reactivar/<int:id_tarea>")
def reactivar_tarea(id_tarea):
    tarea = TareaModel.obtener_por_id(id_tarea)

    if tarea["estado"] == "Finalizada":
        TareaModel.reactivar(id_tarea)
        flash("Tarea reactivada correctamente.")
    else:
        flash("Solo se puede reactivar una tarea finalizada.")

    return redirect(url_for("tareas.listar_tareas"))


@tareas_bp.route("/cambiar_estado/<int:id_tarea>", methods=["POST"])
def cambiar_estado(id_tarea):
    nuevo_estado = request.form["estado"]
    tarea = TareaModel.obtener_por_id(id_tarea)

    estado_actual = tarea["estado"]

    cambios_validos = {
        "Pendiente": ["En progreso"],
        "En progreso": ["Pendiente", "Bloqueada", "Finalizada"],
        "Bloqueada": ["En progreso"],
        "Finalizada": []
    }

    if nuevo_estado in cambios_validos[estado_actual]:
        if nuevo_estado == "Finalizada":
            TareaModel.finalizar(id_tarea)
        else:
            TareaModel.cambiar_estado(id_tarea, nuevo_estado)

        flash("Estado actualizado correctamente.")
    else:
        flash("Cambio de estado no permitido.")

    return redirect(url_for("tareas.listar_tareas"))