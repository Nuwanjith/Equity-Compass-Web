import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense

# 1. Load and prepare data
df = pd.read_csv('stock_price-TYRE.csv')
df['Date'] = pd.to_datetime(df['Date'], format='%d/%m/%Y')
df = df.sort_values('Date')

# Use only 'Close' price for prediction
data = df[['Close']].values

# 2. Normalize data (LSTMs need values between 0-1)
scaler = MinMaxScaler(feature_range=(0, 1))
scaled_data = scaler.fit_transform(data)

# 3. Create time sequences
def create_sequences(data, seq_length):
    X, y = [], []
    for i in range(len(data)-seq_length-1):
        X.append(data[i:(i+seq_length)])
        y.append(data[i+seq_length])
    return np.array(X), np.array(y)

SEQ_LENGTH = 30  # Use 30 days to predict next day
X, y = create_sequences(scaled_data, SEQ_LENGTH)

# 4. Split into train/test (80/20)
split = int(0.8 * len(X))
X_train, X_test = X[:split], X[split:]
y_train, y_test = y[:split], y[split:]

# 5. Build LSTM model
model = Sequential([
    LSTM(50, activation='relu', input_shape=(SEQ_LENGTH, 1)),
    Dense(1)
])
model.compile(optimizer='adam', loss='mse')

# 6. Train the model
history = model.fit(
    X_train, y_train,
    epochs=20,
    batch_size=32,
    validation_data=(X_test, y_test),
    verbose=1
)

# 7. Make predictions
test_predictions = model.predict(X_test)
test_predictions = scaler.inverse_transform(test_predictions)  # Undo scaling

# 8. Visualize results
plt.figure(figsize=(12,6))
plt.plot(df['Date'][-len(y_test):], scaler.inverse_transform(y_test), label='True Price')
plt.plot(df['Date'][-len(y_test):], test_predictions, label='Predicted Price')
plt.title('Stock Price Prediction (LSTM Only)')
plt.xlabel('Date')
plt.ylabel('Price')
plt.legend()
plt.show()

# 9. Predict next day's price
last_sequence = scaled_data[-SEQ_LENGTH:]  # Last 30 days
next_day_pred = model.predict(last_sequence.reshape(1, SEQ_LENGTH, 1))
next_day_price = scaler.inverse_transform(next_day_pred)[0][0]
print(f"\nNext day predicted price: ${next_day_price:.2f}")