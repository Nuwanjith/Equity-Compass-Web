from flask import Blueprint, jsonify
from app.services.prediction_service import get_prediction
from datetime import datetime

predictions_bp = Blueprint('predictions', __name__)

@predictions_bp.route('/api/predict/<ticker>')
def predict(ticker):
    try:
        result = get_prediction(ticker)
        return jsonify({
            'data': {
                'ticker': ticker,
                'predictedPrice': result['price'],
                'eps': result['eps'],
                'nav': result['nav'],
                'lastUpdated': datetime.now().isoformat()
            },
            'timestamp': datetime.now().isoformat(),
            'error': None
        })
    except Exception as e:
        return jsonify({
            'data': None,
            'error': str(e),
            'timestamp': datetime.now().isoformat()
        }), 500