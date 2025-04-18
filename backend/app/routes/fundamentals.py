from flask import Blueprint

fundamentals_bp = Blueprint('fundamentals', __name__)

@fundamentals_bp.route('/fundamentals')
def get_fundamentals():
    return "Fundamentals endpoint"