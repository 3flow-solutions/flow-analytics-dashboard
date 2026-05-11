import React, { useState, useEffect } from 'react';
import { BarChart, Bar, LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { promoApi } from '../services/api';
import MetricCard from './MetricCard';

function FlowPromoMetrics({ filters }) {
  const [metrics, setMetrics] = useState({
    claimed: 0,
    paid: 0,
    redeemed: 0,
    redemptionRate: 0,
    speedData: [],
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

      const [claimedRes, paidRes, redeemedRes, rateRes, speedRes] = await Promise.all([
        promoApi.getClaimed(params),
        promoApi.getPaid(params),
        promoApi.getRedeemed(params),
        promoApi.getRedemptionRate(params),
        promoApi.getSpeed(params),
      ]);

      const claimed = claimedRes.data.value || 0;
      const paid = paidRes.data.value || 0;
      const redeemed = redeemedRes.data.value || 0;
      const rate = paid > 0 ? ((redeemed / paid) * 100).toFixed(2) : 0;

      setMetrics({
        claimed,
        paid,
        redeemed,
        redemptionRate: rate,
        speedData: speedRes.data.data || [],
      });
    } catch (error) {
      console.error('Failed to load FlowPromo metrics:', error);
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
        <h2 className="text-2xl font-bold text-black mb-4">🎯 FlowPromo Metrics</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <MetricCard label="Claimed" value={metrics.claimed.toLocaleString()} icon="✅" />
          <MetricCard label="Paid" value={metrics.paid.toLocaleString()} icon="💳" />
          <MetricCard label="Redeemed" value={metrics.redeemed.toLocaleString()} icon="🎁" />
          <MetricCard label="Redemption Rate" value={`${metrics.redemptionRate}%`} icon="📊" />
        </div>
      </div>

      {/* Sell-Out Speed Chart */}
      <div className="card p-6">
        <h3 className="text-lg font-semibold text-black mb-4">Sell-Out Speed</h3>
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={metrics.speedData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Line type="monotone" dataKey="speed" stroke="#983400" strokeWidth={2} />
          </LineChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

export default FlowPromoMetrics;
