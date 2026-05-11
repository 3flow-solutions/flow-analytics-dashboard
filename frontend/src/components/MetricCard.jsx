import React, { useState } from 'react';
import { TrendingUp, TrendingDown } from 'lucide-react';

function MetricCard({ label, value, change, icon = '📊', details = [] }) {
  const [showDetails, setShowDetails] = useState(false);
  const isPositive = change >= 0;

  return (
    <div
      className="card p-6 cursor-pointer"
      onMouseEnter={() => setShowDetails(true)}
      onMouseLeave={() => setShowDetails(false)}
    >
      <div className="flex items-start justify-between mb-4">
        <span className="text-2xl">{icon}</span>
        {showDetails && (
          <div className={`flex items-center gap-1 text-xs font-semibold ${
            isPositive ? 'text-green-600' : 'text-red-600'
          }`}>
            {isPositive ? <TrendingUp size={14} /> : <TrendingDown size={14} />}
            <span>{Math.abs(change)}% vs last period</span>
          </div>
        )}
      </div>

      <p className="metric-label">{label}</p>
      <p className="metric-value mt-2">{value}</p>

      {showDetails && details.length > 0 && (
        <div className="mt-4 pt-4 border-t border-gray-200 space-y-2">
          {details.map((detail, idx) => (
            <div key={idx} className="flex justify-between text-xs text-gray-600">
              <span>{detail.label}</span>
              <span className="font-semibold text-gray-900">{detail.value}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default MetricCard;
