from flask import Blueprint, render_template, request, redirect, url_for, flash
from models.responsable_model import ResponsableModel

responsables_bp = Blueprint('responsables', __name__, url_prefix='/responsables')

@responsables_bp.route('/')
def listar_responsables():
    model = ResponsableModel()
    responsables = model.obtener_todos()
    return render_template('responsables/listar.html', responsables=responsables)

@responsables_bp.route('/crear', methods=['GET', 'POST'])
def crear_responsable():
    if request.method == 'POST':
        nombre = request.form.get('nombre', '').strip()
        apellidos = request.form.get('apellidos', '').strip()
        identificacion = request.form.get('identificacion', '').strip()
        
        # Validaciones
        if not nombre:
            flash('El nombre es obligatorio')
            return render_template('responsables/crear.html')
        
        if not apellidos:
            flash('Los apellidos son obligatorios')
            return render_template('responsables/crear.html')
        
        if not identificacion:
            flash('La identificación es obligatoria')
            return render_template('responsables/crear.html')
        
        model = ResponsableModel()
        
        # Verificar que no exista duplicado
        if model.existe_identificacion(identificacion):
            flash('Ya existe un responsable con esa identificación')
            return render_template('responsables/crear.html')
        
        try:
            responsable_id = model.crear(nombre, apellidos, identificacion)
            if responsable_id:
                flash('Responsable creado exitosamente')
                return redirect(url_for('responsables.listar_responsables'))
            else:
                flash('Error al crear el responsable')
        except Exception as e:
            flash(f'Error al crear el responsable: {str(e)}')
    
    return render_template('responsables/crear.html')

@responsables_bp.route('/editar/<int:responsable_id>', methods=['GET', 'POST'])
def editar_responsable(responsable_id):
    model = ResponsableModel()
    responsable = model.obtener_por_id(responsable_id)
    
    if not responsable:
        flash('Responsable no encontrado')
        return redirect(url_for('responsables.listar_responsables'))
    
    if request.method == 'POST':
        nombre = request.form.get('nombre', '').strip()
        apellidos = request.form.get('apellidos', '').strip()
        identificacion = request.form.get('identificacion', '').strip()
        
        # Validaciones
        if not nombre:
            flash('El nombre es obligatorio')
            return render_template('responsables/editar.html', responsable=responsable)
        
        if not apellidos:
            flash('Los apellidos son obligatorios')
            return render_template('responsables/editar.html', responsable=responsable)
        
        if not identificacion:
            flash('La identificación es obligatoria')
            return render_template('responsables/editar.html', responsable=responsable)
        
        # Verificar que no exista duplicado
        if model.existe_identificacion(identificacion, responsable_id):
            flash('Ya existe otro responsable con esa identificación')
            return render_template('responsables/editar.html', responsable=responsable)
        
        try:
            if model.actualizar(responsable_id, nombre, apellidos, identificacion):
                flash('Responsable actualizado exitosamente')
                return redirect(url_for('responsables.listar_responsables'))
            else:
                flash('Error al actualizar el responsable')
        except Exception as e:
            flash(f'Error al actualizar el responsable: {str(e)}')
    
    return render_template('responsables/editar.html', responsable=responsable)

@responsables_bp.route('/eliminar/<int:responsable_id>', methods=['POST'])
def eliminar_responsable(responsable_id):
    model = ResponsableModel()
    responsable = model.obtener_por_id(responsable_id)
    
    if not responsable:
        flash('Responsable no encontrado')
        return redirect(url_for('responsables.listar_responsables'))
    
    try:
        if model.eliminar(responsable_id):
            flash('Responsable eliminado exitosamente')
        else:
            flash('Error al eliminar el responsable')
    except Exception as e:
        flash(f'Error al eliminar el responsable: {str(e)}')
    
    return redirect(url_for('responsables.listar_responsables'))

@responsables_bp.route('/ver/<int:responsable_id>')
def ver_responsable(responsable_id):
    model = ResponsableModel()
    responsable = model.obtener_por_id(responsable_id)
    
    if not responsable:
        flash('Responsable no encontrado')
        return redirect(url_for('responsables.listar_responsables'))
    
    tareas = model.obtener_tareas(responsable_id)
    return render_template('responsables/ver.html', responsable=responsable, tareas=tareas)