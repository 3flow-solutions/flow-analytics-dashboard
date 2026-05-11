import React, { useState, useEffect } from 'react';
import { BarChart, Bar, LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { referralApi } from '../services/api';
import MetricCard from './MetricCard';

function FlowLinkMetrics({ filters }) {
  const [metrics, setMetrics] = useState({
    links: 0,
    conversions: 0,
    revenueData: [],
    topReferrers: [],
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

      const [linksRes, conversionsRes, revenueRes, topRes] = await Promise.all([
        referralApi.getLinks(params),
        referralApi.getConversions(params),
        referralApi.getRevenue(params),
        referralApi.getTopReferrers(params),
      ]);

      setMetrics({
        links: linksRes.data.value || 0,
        conversions: conversionsRes.data.value || 0,
        revenueData: revenueRes.data.data || [],
        topReferrers: topRes.data.data || [],
      });
    } catch (error) {
      console.error('Failed to load FlowLink metrics:', error);
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
        <h2 className="text-2xl font-bold text-black mb-4">🔗 FlowLink Metrics</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <MetricCard label="Referral Links" value={metrics.links.toLocaleString()} icon="🔗" />
          <MetricCard label="Conversions" value={metrics.conversions.toLocaleString()} icon="✨" />
        </div>
      </div>

      {/* Revenue Chart */}
      <div className="card p-6">
        <h3 className="text-lg font-semibold text-black mb-4">Referral Revenue</h3>
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={metrics.revenueData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="date" />
            <YAxis />
            <Tooltip formatter={(value) => `$${value.toLocaleString()}`} />
            <Legend />
            <Line type="monotone" dataKey="revenue" stroke="#D4AF37" strokeWidth={2} />
          </LineChart>
        </ResponsiveContainer>
      </div>

      {/* Top Referrers Leaderboard */}
      <div className="card p-6">
        <h3 className="text-lg font-semibold text-black mb-4">🏆 Top Referrers</h3>
        <div className="space-y-3">
          {metrics.topReferrers.length > 0 ? (
            metrics.topReferrers.map((referrer, idx) => (
              <div key={idx} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div className="flex items-center gap-3">
                  <div className="h-8 w-8 rounded-full bg-red-900 text-white flex items-center justify-center text-xs font-bold">
                    {idx + 1}
                  </div>
                  <div>
                    <p className="font-semibold text-black">{referrer.name}</p>
                    <p className="text-xs text-gray-600">{referrer.email}</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="font-bold text-black">${referrer.revenue.toLocaleString()}</p>
                  <p className="text-xs text-gray-600">{referrer.conversions} conversions</p>
                </div>
              </div>
            ))
          ) : (
            <p className="text-gray-600 text-center py-4">No referrer data available</p>
          )}
        </div>
      </div>
    </div>
  );
}

export default FlowLinkMetrics;
