import React, { useState, useEffect } from 'react';
import { BarChart, Bar, LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { loyaltyApi } from '../services/api';
import MetricCard from './MetricCard';

function FlowLoyaltyMetrics({ filters }) {
  const [metrics, setMetrics] = useState({
    stamps: 0,
    rewards: 0,
    repeatRate: 0,
    clvData: [],
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadMetrics();
  }, [filters]);

  const loadMetrics = async () => {
    setLoading(true);
    try {
      const params = {
        campaign: filters.campaign,
        start_date: filters.startDate.toISOString().split('T')[0],
        end_date: filters.endDate.toISOString().split('T')[0],
      };

      const [stampsRes, rewardsRes, rateRes, clvRes] = await Promise.all([
        loyaltyApi.getStamps(params),
        loyaltyApi.getRewards(params),
        loyaltyApi.getRepeatRate(params),
        loyaltyApi.getClv(params),
      ]);

      setMetrics({
        stamps: stampsRes.data.value || 0,
        rewards: rewardsRes.data.value || 0,
        repeatRate: rateRes.data.value || 0,
        clvData: clvRes.data.data || [],
      });
    } catch (error) {
      console.error('Failed to load FlowLoyalty metrics:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="h-96 bg-white rounded-lg animate-pulse"></div>;
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-bold text-black mb-4">⭐ FlowLoyalty Metrics</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <MetricCard label="Stamps" value={metrics.stamps.toLocaleString()} icon="🎫" />
          <MetricCard label="Rewards" value={metrics.rewards.toLocaleString()} icon="🏆" />
          <MetricCard label="Repeat Purchase Rate" value={`${metrics.repeatRate}%`} icon="🔄" />
        </div>
      </div>

      {/* CLV Chart */}
      <div className="card p-6">
        <h3 className="text-lg font-semibold text-black mb-4">Customer Lifetime Value (CLV)</h3>
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={metrics.clvData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis />
            <Tooltip formatter={(value) => `$${value.toLocaleString()}`} />
            <Legend />
            <Line type="monotone" dataKey="clv" stroke="#22C55E" strokeWidth={2} />
          </LineChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

export default FlowLoyaltyMetrics;
