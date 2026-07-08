# bondoo - Bontang Odoo SaaS 🚀

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Laravel](https://img.shields.io/badge/laravel-v10-red)
![PHP](https://img.shields.io/badge/php-v8.4-blueviolet)
![Bootstrap](https://img.shields.io/badge/bootstrap-v4.6-purple)
![TailwindCSS](https://img.shields.io/badge/tailwindcss-utilities-38bdf8)

**bondoo** (Bontang Odoo SaaS) is a premium, enterprise-grade **Point of Sale (POS)** and **ERP / Inventory Management** system. Featuring a custom Apple-inspired design (contrast-first dark glassmorphism, Apple Blue `#0071e3`, and Outfit/Inter typography), it is tailor-made for modern retail, wholesale, and multi-unit business operations.

---

## ✨ Features & Modules

### 🛒 Advanced Point of Sale (POS)
* **Multi-Unit & Packaging Conversions:** Sell in base units (e.g., *pcs*) or bulk units (e.g., *dus*, *box*, *pack*) with dynamic conversion multipliers.
* **Tiered Pricing (Harga Bertingkat):** Automated pricing adjustments based on purchase quantities (wholesale/grosir vs retail).
* **Thermal Receipt Printing:** Optimized 58mm layout with automatic static QRIS code generation for cashier transactions.
* **Dynamic Cart:** Real-time subtotal, discount, and tax calculations powered by `hardevine/shoppingcart`.

### 📦 Procurement, Opname, & Stock Management
* **Purchase Orders (PO):** Procurement workflow from master supplier registry to pending approval, recording stock arrivals and accounts payable.
* **Stock Opname (Penyesuaian Stok):** Log physical vs system inventory count discrepancies with detailed reasons.
* **Customer & Supplier Returns:** Dedicated refund/exchange registers for sales and purchases.

### 💰 Financial & Analytics Reports
* **Interactive Dashboard:** Core metrics, today's snapshot, top-5 best selling products, and monthly sales graphs.
* **Export & Print Layouts:** Print clean, printer-friendly reports for custom date ranges.

### 👥 HR & Access Control
* **Employee Management:** Track employee information, salaries, and daily attendance logs.
* **Granular RBAC:** Role and permission settings powered by `spatie/laravel-permission` to secure sensitive fields.

---

## 🛠️ Installation Guide

Follow these steps to set up the project locally:

### 1. Clone the Repository
```bash
git clone https://github.com/fajarghifar/laravel-point-of-sale bondoo
cd bondoo
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run dev
```

### 3. Environment Setup
Copy the example environment file and configure your credentials:
```bash
cp .env.example .env
# Open .env and set your database (SQLite is supported out-of-the-box)
```

Generate the encryption key:
```bash
php artisan key:generate
```

### 4. Run Migrations & Seeding
```bash
php artisan migrate:fresh --seed
```

### 5. Create Storage Link
```bash
php artisan storage:link
```

---

## 🔑 Default Credentials

Access the SuperAdmin dashboard with:

| Role | Username | Password |
| --- | --- | --- |
| **SuperAdmin** | `admin` | `password` |

---

