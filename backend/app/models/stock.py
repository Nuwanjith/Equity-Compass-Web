from app.extensions import db

class StockFundamental(db.Model):
    __tablename__ = 'kelani_tyre_quater_performance'
    
    id = db.Column(db.Integer, primary_key=True)
    ticker = db.Column(db.String(10), nullable=False)
    quarter = db.Column(db.Date, nullable=False)
    eps = db.Column(db.Float)
    nav = db.Column(db.Float)
    
    def __repr__(self):
        return f'<Stock {self.ticker} Q{self.quarter}>'