# 📊 FlowAnalytics Dashboard

A comprehensive analytics dashboard for tracking **FlowPromo**, **FlowLoyalty**, and **FlowLink** metrics with real-time data visualization.

## 🎯 Features

### FlowPromo Metrics
- Claimed offers count
- Paid orders
- Redeemed transactions
- Redemption rate (gauge chart)
- Sell-out speed (line chart)

### FlowLoyalty Metrics
- Stamps issued
- Rewards claimed
- Repeat purchase rate (bar chart)
- Customer lifetime value - CLV (line chart)

### FlowLink Metrics
- Referral links generated
- Conversion funnel
- Referral revenue (line chart)
- Top referrers leaderboard

### Dashboard Features
- 📱 Responsive design (3-col desktop, stacked mobile)
- 🎨 Modern card-based UI with Stripe-like design
- 📅 Date range picker with presets (Today, 7 days, 30 days)
- 🔄 Weekly/Monthly toggle
- 📊 Interactive charts (Recharts)
- 🏷️ Campaign selector dropdown
- 📈 Hover cards showing % change vs previous period
- 💾 Export to CSV/Excel
- 🔐 JWT authentication

## 🛠️ Tech Stack

**Frontend:**
- React 18
- Recharts (data visualization)
- TailwindCSS (styling)
- Axios (HTTP client)
- Lucide React (icons)

**Backend:**
- PHP 8.1+
- Laravel 10 (optional, or vanilla PHP)
- MySQL 8.0+
- JWT Authentication

## 📦 Installation

### Prerequisites
- Node.js 16+
- PHP 8.1+
- MySQL 8.0+
- Composer

### Frontend Setup

```bash
# Install dependencies
npm install

# Start development server
npm start

# Build for production
npm run build
```

### Backend Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate JWT secret
php artisan key:generate

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed

# Start server
php artisan serve
```

### Database Setup

```bash
# Create database
mysql -u root -e "CREATE DATABASE flow_analytics;"

# Import schema
mysql -u root flow_analytics < database/schema.sql
```

## 📁 Project Structure

```
flow-analytics-dashboard/
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Dashboard.jsx
│   │   │   ├── FlowPromoMetrics.jsx
│   │   │   ├── FlowLoyaltyMetrics.jsx
│   │   │   ├── FlowLinkMetrics.jsx
│   │   │   ├── MetricCard.jsx
│   │   │   ├── FilterBar.jsx
│   │   │   └── SummaryBar.jsx
│   │   ├── pages/
│   │   │   ├── Login.jsx
│   │   │   └── Dashboard.jsx
│   │   ├── services/
│   │   │   ├── api.js
│   │   │   └── auth.js
│   │   ├── App.jsx
│   │   ├── index.css
│   │   └── index.js
│   ├── public/
│   ├── package.json
│   └── tailwind.config.js
├── backend/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── PromoMetricsController.php
│   │   │   │   ├── LoyaltyMetricsController.php
│   │   │   │   └── ReferralMetricsController.php
│   │   │   └── Middleware/
│   │   │       └── JwtMiddleware.php
│   │   └── Models/
│   │       ├── Order.php
│   │       ├── Offer.php
│   │       ├── LoyaltyCustomer.php
│   │       ├── LoyaltyStamp.php
│   │       └── LoyaltyTrustLog.php
│   ├── routes/
│   │   └── api.php
│   ├── database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── schema.sql
│   ├── .env.example
│   ├── composer.json
│   └── server.php
├── docs/
│   ├── API.md
│   ├── DATABASE.md
│   └── DEPLOYMENT.md
└── package.json
```

## 🔌 API Endpoints

### Authentication
```
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me
```

### FlowPromo Metrics
```
GET    /api/promos/claimed?campaign={id}&range={date}
GET    /api/promos/paid?campaign={id}&range={date}
GET    /api/promos/redeemed?campaign={id}&range={date}
GET    /api/promos/redemption-rate?campaign={id}&range={date}
GET    /api/promos/speed?campaign={id}&range={date}
```

### FlowLoyalty Metrics
```
GET    /api/loyalty/stamps?campaign={id}&range={date}
GET    /api/loyalty/rewards?campaign={id}&range={date}
GET    /api/loyalty/repeat-rate?campaign={id}&range={date}
GET    /api/loyalty/clv?campaign={id}&range={date}
```

### FlowLink Metrics
```
GET    /api/referrals/links?campaign={id}&range={date}
GET    /api/referrals/conversions?campaign={id}&range={date}
GET    /api/referrals/revenue?campaign={id}&range={date}
GET    /api/referrals/top?campaign={id}&range={date}
```

### Summary
```
GET    /api/summary/revenue?range={date}
GET    /api/summary/repeat?range={date}
GET    /api/summary/referrals?range={date}
GET    /api/summary/predictability?range={date}
```

### Data Export
```
GET    /api/export?type=csv&metrics=promos,loyalty,referrals&range={date}
```

## 🎨 Design

- **Color Scheme**: White background, #983400 (red-brown) + black with gold & green accents
- **Icons**: Minimalist Lucide React icons (Stripe-inspired)
- **Layout**: Card-based, responsive grid
- **Typography**: Clean, modern fonts

## 🔐 Security

- JWT token-based authentication
- CORS configuration
- Input validation on frontend & backend
- SQL injection prevention with prepared statements
- Environment variables for sensitive data

## 📊 Database Schema

See `docs/DATABASE.md` for complete schema documentation.

### Tables Used
- `offers` - Campaign offers for FlowPromo
- `orders` - Order transactions (includes FlowPromo, referral, payment data)
- `loyalty_customers` - Loyalty program customers
- `loyalty_stamps` - Stamp card tracking
- `loyalty_trust_log` - Loyalty transaction audit log

## 🚀 Deployment

See `docs/DEPLOYMENT.md` for production deployment guide.

## 📝 License

Private - 3flow Solutions

## 📧 Support

For issues or questions, contact: 3flowagency@gmail.com
