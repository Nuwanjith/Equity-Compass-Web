import os

class Config:
    SQLALCHEMY_DATABASE_URI = os.getenv('DATABASE_URL', 'mysql+pymysql://root:root53421@localhost/Equity_compass_poc')
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    MODEL_PATH = os.path.join(os.path.dirname(__file__), '../../ml_models/kelani_tyre_model.txt')