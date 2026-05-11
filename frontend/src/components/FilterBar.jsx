import React, { useState, useEffect } from 'react';
import { Calendar, Filter } from 'lucide-react';
import { promoApi } from '../services/api';
import { format, subDays } from 'date-fns';

function FilterBar({ filters, setFilters }) {
  const [campaigns, setCampaigns] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadCampaigns();
  }, []);

  const loadCampaigns = async () => {
    try {
      const response = await promoApi.getCampaigns();
      setCampaigns(response.data);
    } catch (error) {
      console.error('Failed to load campaigns:', error);
    } finally {
      setLoading(false);
    }
  };

  const getDateRange = (range) => {
    const today = new Date();
    switch (range) {
      case 'today':
        return { startDate: today, endDate: today };
      case '7days':
        return { startDate: subDays(today, 7), endDate: today };
      case '30days':
        return { startDate: subDays(today, 30), endDate: today };
      default:
        return { startDate: today, endDate: today };
    }
  };

  const handleDateRangeChange = (range) => {
    const { startDate, endDate } = getDateRange(range);
    setFilters({
      ...filters,
      dateRange: range,
      startDate,
      endDate,
    });
  };

  return (
    <div className="mb-8 p-6 bg-white border border-gray-200 rounded-lg">
      <h2 className="text-lg font-semibold text-black mb-4 flex items-center gap-2">
        <Filter size={20} />
        Filters & Controls
      </h2>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Campaign Selector */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Campaign
          </label>
          <select
            value={filters.campaign || ''}
            onChange={(e) => setFilters({ ...filters, campaign: e.target.value || null })}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent outline-none"
          >
            <option value="">All Campaigns</option>
            {campaigns.map((campaign) => (
              <option key={campaign.id} value={campaign.id}>
                {campaign.offer_title}
              </option>
            ))}
          </select>
        </div>

        {/* Date Range Presets */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Date Range
          </label>
          <select
            value={filters.dateRange}
            onChange={(e) => handleDateRangeChange(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-900 focus:border-transparent outline-none"
          >
            <option value="today">Today</option>
            <option value="7days">Last 7 Days</option>
            <option value="30days">Last 30 Days</option>
          </select>
        </div>

        {/* Frequency Toggle */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Frequency
          </label>
          <div className="flex gap-2">
            {['daily', 'weekly', 'monthly'].map((freq) => (
              <button
                key={freq}
                onClick={() => setFilters({ ...filters, frequency: freq })}
                className={`flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                  filters.frequency === freq
                    ? 'bg-red-900 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                }`}
              >
                {freq.charAt(0).toUpperCase() + freq.slice(1)}
              </button>
            ))}
          </div>
        </div>

        {/* Custom Date Range */}
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            <Calendar size={16} className="inline mr-2" />
            Custom Range
          </label>
          <div className="flex gap-2">
            <input
              type="date"
              value={format(filters.startDate, 'yyyy-MM-dd')}
              onChange={(e) => setFilters({ ...filters, startDate: new Date(e.target.value) })}
              className="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-900 focus:border-transparent outline-none"
            />
          </div>
        </div>
      </div>
    </div>
  );
}

export default FilterBar;
