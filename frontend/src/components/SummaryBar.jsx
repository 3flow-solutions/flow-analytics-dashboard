import React, { useState, useEffect } from 'react';
import { TrendingUp } from 'lucide-react';
import { summaryApi } from '../services/api';
import MetricCard from './MetricCard';

function SummaryBar({ filters }) {
  const [summary, setSummary] = useState({
    revenue: 0,
    repeatCustomers: 0,
    referralSales: 0,
    predictabilityScore: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadSummary();
  }, [filters]);

  const loadSummary = async () => {
    setLoading(true);
    try {
      const params = {
        campaign: filters.campaign,
        start_date: filters.startDate.toISOString().split('T')[0],
        end_date: filters.endDate.toISOString().split('T')[0],
      };

      const [revenueRes, repeatRes, referralRes, scoreRes] = await Promise.all([
        summaryApi.getRevenue(params),
        summaryApi.getRepeatCustomers(params),
        summaryApi.getReferralSales(params),
        summaryApi.getPredictabilityScore(params),
      ]);

      setSummary({
        revenue: revenueRes.data.value || 0,
        repeatCustomers: repeatRes.data.value || 0,
        referralSales: referralRes.data.value || 0,
        predictabilityScore: scoreRes.data.value || 0,
      });
    } catch (error) {
      console.error('Failed to load summary:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="h-32 bg-white rounded-lg animate-pulse"></div>;
  }

  return (
    <div className="mb-8">
      <h2 className="text-lg font-semibold text-black mb-4 flex items-center gap-2">
        <TrendingUp size={20} />
        Summary Metrics
      </h2>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <MetricCard
          label="Guaranteed Revenue"
          value={`$${summary.revenue.toLocaleString()}`}
          change={12.5}
          icon="💰"
        />
        <MetricCard
          label="Repeat Customers"
          value={summary.repeatCustomers.toLocaleString()}
          change={8.2}
          icon="👥"
        />
        <MetricCard
          label="Referral Sales"
          value={`$${summary.referralSales.toLocaleString()}`}
          change={15.3}
          icon="🔗"
        />
        <MetricCard
          label="Predictability Score"
          value={`${summary.predictabilityScore}%`}
          change={5.1}
          icon="📈"
        />
      </div>
    </div>
  );
}

export default SummaryBar;
