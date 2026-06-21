from flask import Blueprint, render_template, redirect, url_for
from models.tarea_model import TareaModel
from flask import request
from models.tarea_model import TareaModel


tablero_bp = Blueprint("tablero", __name__)

@tablero_bp.route("/tablero")
def tablero():

    estado = request.args.get("estado")
    prioridad = request.args.get("prioridad")
    fecha = request.args.get("fecha")

    tareas = TareaModel.obtener_todas_filtradas(estado, prioridad, fecha)

    pendientes = [t for t in tareas if t["estado"] == "Pendiente"]
    progreso = [t for t in tareas if t["estado"] == "En progreso"]
    finalizadas = [t for t in tareas if t["estado"] == "Finalizada"]

    return render_template(
        "tableros/tablero.html",
        pendientes=pendientes,
        progreso=progreso,
        finalizadas=finalizadas
    )

@tablero_bp.route("/tablero/cambiar_estado/<int:id_tarea>/<estado>")
def cambiar_estado(id_tarea, estado):

    if estado == "Finalizada":
        TareaModel.finalizar(id_tarea)
    else:
        TareaModel.cambiar_estado(id_tarea, estado)

    return redirect(url_for("tablero.tablero"))