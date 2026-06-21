from flask import Flask, redirect, url_for


from routes.tareas_routes import tareas_bp
from routes.grupos_routes import grupos_bp
from routes.responsables_routes import responsables_bp
from routes.tableros_routes import tablero_bp



app = Flask(__name__)

app.secret_key = "clave_secreta_control_tareas"

app.register_blueprint(tareas_bp)
app.register_blueprint(grupos_bp)
app.register_blueprint(responsables_bp)
app.register_blueprint(tablero_bp)



@app.route("/")
def inicio():
    return redirect(url_for("tareas.listar_tareas"))


if __name__ == "__main__":
    app.run(debug=True)