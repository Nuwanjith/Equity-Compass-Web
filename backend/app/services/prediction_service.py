import lightgbm as lgb
import pandas as pd
from app.models.stock import StockFundamental
from app.extensions import db

model = None

def load_model():
    global model
    if model is None:
        from app import current_app
        model = lgb.Booster(model_file=current_app.config['MODEL_PATH'])
    return model

def get_prediction(ticker):
    # Get latest fundamentals
    fundamental = db.session.execute(
        db.select(StockFundamental)
        .filter_by(ticker=ticker)
        .order_by(StockFundamental.quarter.desc())
    ).scalar_one()
    
    # Prepare features
    features = pd.DataFrame({
        'prev_eps': [fundamental.eps],
        'prev_nav': [fundamental.nav],
        # Add other features
    })
    
    # Predict
    model = load_model()
    price = model.predict(features)[0]
    
    return {
        'price': float(price),
        'eps': fundamental.eps,
        'nav': fundamental.nav
    }