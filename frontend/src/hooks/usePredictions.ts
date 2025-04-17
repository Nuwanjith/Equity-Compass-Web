// src/hooks/usePredictions.ts
import { useQuery } from '@tanstack/react-query';
import axios from 'axios';
import { StockPrediction, ApiResponse } from '../types'; // Added ApiResponse import

const fetchPredictions = async (ticker: string): Promise<StockPrediction> => {
  const { data } = await axios.get<ApiResponse<StockPrediction>>(
    `/api/predict?ticker=${ticker}`
  );
  
  if (data.error) {
    throw new Error(data.error);
  }
  
  return {
    ...data.data,
    lastUpdated: new Date().toISOString() // Ensure proper date format
  };
};

export const usePredictions = (ticker: string) => {
  return useQuery({
    queryKey: ['predictions', ticker],
    queryFn: () => fetchPredictions(ticker),
    refetchInterval: 3600000, // 1 hour
    select: (data) => ({
      ...data,
      lastUpdated: new Date(data.lastUpdated) // Convert back to Date object
    })
  });
};