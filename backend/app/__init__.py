from flask import Flask
from .extensions import db, migrate
from .config import Config

def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)
    
    # Initialize extensions
    db.init_app(app)
    migrate.init_app(app, db)
    
    # Register blueprints
    from .routes.predictions import predictions_bp
    from .routes.fundamentals import fundamentals_bp
    
    app.register_blueprint(predictions_bp)
    app.register_blueprint(fundamentals_bp)
    
    return app