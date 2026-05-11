import axios from 'axios';
import { getToken } from './auth';

const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add JWT token to requests
api.interceptors.request.use(
  (config) => {
    const token = getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Handle responses
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// API Methods
export const authApi = {
  login: (email, password) => api.post('/auth/login', { email, password }),
  logout: () => api.post('/auth/logout'),
  getProfile: () => api.get('/auth/me'),
};

export const promoApi = {
  getCampaigns: () => api.get('/campaigns'),
  getClaimed: (params) => api.get('/promos/claimed', { params }),
  getPaid: (params) => api.get('/promos/paid', { params }),
  getRedeemed: (params) => api.get('/promos/redeemed', { params }),
  getRedemptionRate: (params) => api.get('/promos/redemption-rate', { params }),
  getSpeed: (params) => api.get('/promos/speed', { params }),
};

export const loyaltyApi = {
  getStamps: (params) => api.get('/loyalty/stamps', { params }),
  getRewards: (params) => api.get('/loyalty/rewards', { params }),
  getRepeatRate: (params) => api.get('/loyalty/repeat-rate', { params }),
  getClv: (params) => api.get('/loyalty/clv', { params }),
};

export const referralApi = {
  getLinks: (params) => api.get('/referrals/links', { params }),
  getConversions: (params) => api.get('/referrals/conversions', { params }),
  getRevenue: (params) => api.get('/referrals/revenue', { params }),
  getTopReferrers: (params) => api.get('/referrals/top', { params }),
};

export const summaryApi = {
  getRevenue: (params) => api.get('/summary/revenue', { params }),
  getRepeatCustomers: (params) => api.get('/summary/repeat', { params }),
  getReferralSales: (params) => api.get('/summary/referrals', { params }),
  getPredictabilityScore: (params) => api.get('/summary/predictability', { params }),
};

export const exportApi = {
  exportData: (params) => api.get('/export', { params, responseType: 'blob' }),
};

export default api;
