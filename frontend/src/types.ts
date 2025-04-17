export interface StockPrediction {
    ticker: string;
    predictedPrice: number;
    lastUpdated: Date;
    eps?: number;
    nav?: number;
  }
  
  export interface ApiResponse<T> {
    data: T;
    error?: string;
    timestamp: string;
  }