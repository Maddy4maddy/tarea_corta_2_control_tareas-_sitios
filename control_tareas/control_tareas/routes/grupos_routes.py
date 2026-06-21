from flask import Blueprint, render_template, request, redirect, url_for, flash
from models.grupo_model import GrupoModel
from models.tarea_model import TareaModel

grupos_bp = Blueprint("grupos", __name__, url_prefix="/grupos")


@grupos_bp.route("/")
def listar_grupos():
    grupos = GrupoModel.obtener_todos()
    return render_template("grupos/listar.html", grupos=grupos)


@grupos_bp.route("/crear", methods=["GET", "POST"])
def crear_grupo():
    tareas_pendientes = GrupoModel.obtener_tareas_pendientes()

    if request.method == "POST":
        nombre = request.form["nombre"]

        id_grupo_creado = GrupoModel.insertar(nombre)

        tareas_seleccionadas = request.form.getlist("tareas")

        for id_tarea in tareas_seleccionadas:
            tarea = TareaModel.obtener_por_id(id_tarea)

            TareaModel.actualizar(
                id_tarea,
                tarea["detalle"],
                tarea["prioridad"],
                tarea["fecha_limite"],
                tarea["id_responsable"],
                id_grupo_creado
            )

        flash("Grupo creado correctamente.")
        return redirect(url_for("grupos.listar_grupos"))

    return render_template(
        "grupos/crear.html",
        tareas_pendientes=tareas_pendientes
    )


@grupos_bp.route("/ver/<int:id_grupo>")
def ver_tareas_grupo(id_grupo):
    tareas = GrupoModel.obtener_tareas_grupo(id_grupo)

    return render_template(
        "grupos/ver_tareas.html",
        tareas=tareas
    )


@grupos_bp.route("/eliminar/<int:id_grupo>")
def eliminar_grupo(id_grupo):
    GrupoModel.eliminar(id_grupo)
    flash("Grupo eliminado correctamente.")

    return redirect(url_for("grupos.listar_grupos"))