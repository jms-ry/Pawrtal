# 🐾 Pawrtal - Animal Rescue Management System

A comprehensive web-based platform for animal rescue organizations to manage rescue operations, facilitate adoptions, and accept donations.

## ✨ Features

### Core Modules
- **🏠 Rescue Management** - Complete CRUD operations for rescue animal profiles
- **📋 Adoption Process** - Digital application system with inspection scheduling
- **🔍 Lost & Found** - Report and search for missing pets
- **💝 Donations** - In-kind and monetary donations via GCash (PayMongo)

### Advanced Features
- **🤖 AI-Driven Pet Matching** - Intelligent recommendation engine using NLP and compatibility scoring
- **🔔 Automated Notifications** - Daily inspection reminders via cron jobs
- **💳 Payment Integration** - Secure GCash payments through PayMongo
- **🪝 Webhook Handling** - Real-time payment status updates
- **👥 Role-Based Access** - Admin, Staff, and Regular User permissions

## 🛠️ Tech Stack

- **Backend:** Laravel 12+, PHP 8.2+
- **Frontend:** Vue 3, Inertia.js, Bootstrap 5
- **Database:** PostgreSQL
- **Payment:** PayMongo API
- **Deployment:** Railway (Production)

## 📦 Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL 14+

### Setup Steps

1. **Clone the repository**
```bash
   git clone https://github.com/jms-ry/Pawrtal.git
   cd Pawrtal
```

2. **Install dependencies**
```bash
   composer install
   npm install
```

3. **Configure environment**
```bash
   cp .env.example .env
   php artisan key:generate
```

4. **Update `.env` with your credentials**
```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=pawrtal
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   PAYMONGO_PUBLIC_KEY=your_paymongo_public_key
   PAYMONGO_SECRET_KEY=your_paymongo_secret_key
```

5. **Run migrations and seed database**
```bash
   php artisan migrate:fresh --seed
```

6. **Build frontend assets**
```bash
   npm run build
```

7. **Start the application**
```bash
   php artisan serve
```

8. **Access the application**
```
   http://localhost:8000
```

## 👤 Default Accounts

After seeding, use these credentials to login:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Staff | staff@example.com | password |
| User | regular@example.com | password |

## 📖 User Guide

### For Adopters

1. **Browse Adoptable Rescues**
   - Visit the Adoption page to view available animals
   - Use filters (size, sex) or search by name

2. **Find Your Perfect Match**
   - Click "Match Me a Rescue" button
   - Fill in your preferences (size, age, energy level, etc.)
   - View personalized recommendations based on compatibility

3. **Submit Adoption Application**
   - Click "Adopt Me" on any rescue profile
   - Complete your profile (address & household info)
   - Fill out the adoption application form
   - Upload required documents (Valid ID, supporting docs)
   - Choose preferred inspection dates

4. **Make a Donation**
   - Click "Donate" in the navigation
   - Choose donation type:
     - **In-Kind:** Upload photos and describe items
     - **Monetary:** Enter amount and pay via GCash
       - Receive confirmation and receipt

5. **Report Lost/Found Pets**
   - Go to Lost & Found page
   - Submit a report with pet details and photo
   - Search existing reports

### For Admin/Staff

1. **Manage Rescues**
   - Add new rescue animals with photos and details
   - Update rescue information and adoption status
   - Soft delete rescues (viewable in trash)

2. **Process Applications**
   - Review submitted adoption applications
   - Schedule inspections
   - Update application status (approve/reject)

3. **Track Donations**
   - View all donations (in-kind and monetary)
   - Monitor payment statuses via PayMongo webhook
   - Accept/reject in-kind donations

4. **Generate Reports**
   - Access dashboard analytics
   - View adoption statistics
   - Monitor user activities


## 🔧 Development

### Run in development mode
```bash
# Terminal 1 - Backend
php artisan serve

# Terminal 2 - Frontend (hot reload)
npm run dev
```

### Clear caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🌐 Production Deployment

Currently deployed on Railway: [pawrtal.up.railway.app](https://pawrtal.up.railway.app)

`Note: The domain might show "Page Not Found" as this was currently deployed using a trial plan.`

## To deploy in `Railway`,
- First, create `Postgres` service
- Next, create new service, select `Github Repository,` and select the repository of this project and edit its environment variables.
  
### Environment Variables (Railway)

Required variables:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (generated)
- `DATABASE_URL` (from Railway Postgres)
- `PAYMONGO_PUBLIC_KEY`
- `PAYMONGO_SECRET_KEY`
- `CRON_SECRET` (for automated tasks)

### Automated Tasks

Set up cron jobs for:
- **Donation Cleanup:** `GET /cron/cleanup-donations?secret=CRON_SECRET` (every 15 min)
- **Inspection Notifications:** `GET /cron/notify-inspections?secret=CRON_SECRET` (daily at midnight)


## 🤝 Contributing

This is a thesis project for academic purposes. For questions or suggestions, please open an issue.

## 👨‍💻 Author

**James Roy**  
BS Computer Science  
Visayas State University

## 📄 License

This project is developed for educational purposes as part of a thesis requirement.
