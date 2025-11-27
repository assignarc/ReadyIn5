<div align="center">

# 🍽️ ReadyIn5

### Simple, Powerful Reservation Management for Restaurants & Venues

[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-777BB4?logo=php)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.x-000000?logo=symfony)](https://symfony.com/)

[Features](#-features) • [Installation](#-installation) • [Configuration](#️-configuration) • [Usage](#-usage) • [Tech Stack](#-tech-stack) • [License](#-license)

</div>

---

## 📖 About

**ReadyIn5** is a modern, mobile-first reservation management system designed for restaurants, cafés, and hospitality venues. Built with Symfony and powered by robust PHP backend, it streamlines the entire reservation lifecycle—from booking to seating to service completion.

Whether you're managing a small café or a bustling restaurant, ReadyIn5 helps you:
- 📅 Accept and manage reservations effortlessly
- ⏰ Track wait times and queue management
- 💬 Communicate with customers via SMS/WhatsApp (Twilio integration)
- 📊 Maintain customer profiles and reservation history
- 📱 Access everything from any mobile browser

---

## ✨ Features

### 🏢 Place Management
- Create and configure multiple venues/locations
- Set business hours, holidays, and closure dates
- Multi-owner support with role-based access

### 📅 Reservation Lifecycle
Complete workflow management with status tracking:
- **Reserve** - Accept new reservations
- **Wait** - Manage waitlist and queue
- **Call** - Notify customers when ready
- **Seat** - Assign tables and seat guests
- **Serve** - Track active service
- **Cancel** - Handle cancellations gracefully

### 📱 Communication
- **Twilio SMS Integration** - Automated customer notifications ✅
- **WhatsApp Integration** - Coming soon 🚧
- Real-time status updates

### 👥 Customer Management
- Comprehensive customer profiles
- Reservation history and preferences
- Contact information management

### 📂 Archive & Analytics
- Historical reservation data
- Archive management for reporting
- Track patterns and optimize operations

### 🌐 Mobile-First Design
- Fully responsive web interface
- Works seamlessly on any device
- No app installation required

---

## 🚀 Installation

### Prerequisites

- **PHP** >= 8.1
- **Composer** (latest version)
- **Database** (MySQL/PostgreSQL/SQLite)
- **Node.js & npm** (for asset compilation)
- **Twilio Account** (for SMS/WhatsApp features)

### Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/assignarc/ReadyIn5.git
   cd ReadyIn5
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env .env.local
   # Edit .env.local with your configuration
   ```

4. **Create database**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start the development server**
   ```bash
   symfony server:start
   # or
   php -S localhost:8000 -t public/
   ```

7. **Access the application**
   
   Open your browser to `http://localhost:8000`

---

## ⚙️ Configuration

### Environment Variables

Update your `.env.local` file with the following configurations:

```env
# Database Configuration
DATABASE_URL="mysql://user:password@127.0.0.1:3306/readyin5?serverVersion=8.0"

# Twilio Integration
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_PHONE_NUMBER=your_twilio_phone

# Application Settings
APP_ENV=prod
APP_SECRET=your_secret_key

# Mailer (optional)
MAILER_DSN=smtp://user:pass@smtp.example.com:587
```

### Database Setup

ReadyIn5 supports multiple database systems:

- **MySQL/MariaDB** (recommended)
- **PostgreSQL**
- **SQLite** (development only)

Update the `DATABASE_URL` in your `.env.local` file accordingly.

### Twilio Setup

1. Sign up for a [Twilio account](https://www.twilio.com/try-twilio)
2. Get your Account SID and Auth Token
3. Purchase a Twilio phone number
4. Add credentials to `.env.local`

---

## 💻 Usage

### Creating a Place

1. Log in to the admin panel
2. Navigate to **Places** → **Add New**
3. Fill in venue details (name, address, capacity, hours)
4. Set holidays and closure dates
5. Add additional owners/managers if needed

### Managing Reservations

**Accept a Reservation:**
- Click **New Reservation**
- Enter customer details and party size
- Select date, time, and preferences
- Confirm reservation

**Queue Management:**
- Walk-in customers automatically join the waitlist
- System tracks wait times automatically
- Call customers when tables are ready

**Status Updates:**
- Move reservations through lifecycle stages
- System sends automated SMS updates
- Track service completion times

### Customer Profiles

- Automatically created on first reservation
- View full reservation history
- Track preferences and special requests
- Export customer data

---

## 🛠️ Tech Stack

**Backend:**
- [Symfony 7](https://symfony.com/) - PHP Framework
- [Doctrine ORM](https://www.doctrine-project.org/) - Database abstraction
- [Twig](https://twig.symfony.com/) - Template engine

**Integrations:**
- [Twilio SDK](https://www.twilio.com/docs/libraries/php) - SMS/WhatsApp
- [Stripe PHP](https://stripe.com/docs/api) - Payment processing (optional)
- [Endroid QR Code](https://github.com/endroid/qr-code) - QR code generation

**Infrastructure:**
- PHP >= 8.1
- Composer dependency management
- Docker support available
- Mobile-responsive design

---

## 📁 Project Structure

```
ReadyIn5/
├── bin/                  # Console scripts
├── config/               # Application configuration
├── migrations/           # Database migrations
├── public/               # Web-accessible files
├── src/                  # Application source code
│   ├── Controller/       # Route controllers
│   ├── Form/            # Form types
│   └── Repository/      # Data repositories
├── templates/           # Twig templates
├── tests/              # Unit & integration tests
├── translations/       # i18n files
├── composer.json       # PHP dependencies
└── .env               # Environment configuration
```

---

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php bin/phpunit

# Run specific test
php bin/phpunit tests/Controller/PlaceControllerTest.php

# Run with coverage
php bin/phpunit --coverage-html coverage/
```

---

## 🐳 Docker Support

Deploy with Docker:

```bash
# Build and start containers
docker-compose up -d

# Run migrations
docker-compose exec php bin/console doctrine:migrations:migrate

# View logs
docker-compose logs -f
```

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the **Apache License 2.0** - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- Built with [Symfony](https://symfony.com/)
- SMS/WhatsApp powered by [Twilio](https://www.twilio.com/)
- Icons and emojis for enhanced UX

---

## 📧 Support

For support, questions, or feature requests:
- Open an issue on [GitHub](https://github.com/assignarc/ReadyIn5/issues)
- Check existing documentation
- Review closed issues for solutions

---

<div align="center">

Made with ❤️ for the hospitality industry

**[⬆ back to top](#️-readyin5)**

</div>