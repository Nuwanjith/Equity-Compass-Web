import { Line } from 'react-chartjs-2';
import { Chart, registerables } from 'chart.js';
import { StockPrediction } from '../types';

Chart.register(...registerables);

interface Props {
  predictions: StockPrediction[];
}

export default function PredictionChart({ predictions }: Props) {
  const chartData = {
    labels: predictions.map(p => p.lastUpdated.toLocaleDateString()),
    datasets: [{
      label: 'Predicted Price (LKR)',
      data: predictions.map(p => p.predictedPrice),
      borderColor: 'rgb(59, 130, 246)',
      tension: 0.1
    }]
  };

  return <Line data={chartData} />;
}