import React, { useState } from 'react';
import { LogOut } from 'lucide-react';
import { removeToken } from '../services/auth';
import FilterBar from '../components/FilterBar';
import SummaryBar from '../components/SummaryBar';
import FlowPromoMetrics from '../components/FlowPromoMetrics';
import FlowLoyaltyMetrics from '../components/FlowLoyaltyMetrics';
import FlowLinkMetrics from '../components/FlowLinkMetrics';

function Dashboard({ onLogout }) {
  const [activeTab, setActiveTab] = useState('all');
  const [filters, setFilters] = useState({
    campaign: null,
    dateRange: 'today',
    startDate: new Date(),
    endDate: new Date(),
    frequency: 'daily',
  });

  const handleLogout = () => {
    removeToken();
    onLogout();
  };

  return (
    <div className="min-h-screen bg-white">
      {/* Header */}
      <header className="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 rounded-lg bg-red-900 text-white flex items-center justify-center font-bold">
              📊
            </div>
            <h1 className="text-2xl font-bold text-black">FlowAnalytics</h1>
          </div>
          <button
            onClick={handleLogout}
            className="flex items-center gap-2 px-4 py-2 bg-gray-100 text-black rounded-lg hover:bg-gray-200 transition-colors"
          >
            <LogOut size={18} />
            <span className="text-sm font-medium">Logout</span>
          </button>
        </div>
      </header>

      {/* Navigation Tabs */}
      <nav className="bg-white border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex gap-8">
            {[
              { id: 'all', label: 'All', icon: '📊' },
              { id: 'promo', label: 'FlowPromo', icon: '🎯' },
              { id: 'loyalty', label: 'FlowLoyalty', icon: '⭐' },
              { id: 'link', label: 'FlowLink', icon: '🔗' },
            ].map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`px-1 py-4 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === tab.id
                    ? 'border-red-900 text-red-900'
                    : 'border-transparent text-gray-600 hover:text-black'
                }`}
              >
                <span className="mr-2">{tab.icon}</span>
                {tab.label}
              </button>
            ))}
          </div>
        </div>
      </nav>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Filters */}
        <FilterBar filters={filters} setFilters={setFilters} />

        {/* Summary Bar */}
        <SummaryBar filters={filters} />

        {/* Metrics Sections */}
        <div className="space-y-8">
          {(activeTab === 'all' || activeTab === 'promo') && (
            <FlowPromoMetrics filters={filters} />
          )}
          {(activeTab === 'all' || activeTab === 'loyalty') && (
            <FlowLoyaltyMetrics filters={filters} />
          )}
          {(activeTab === 'all' || activeTab === 'link') && (
            <FlowLinkMetrics filters={filters} />
          )}
        </div>
      </main>
    </div>
  );
}

export default Dashboard;
