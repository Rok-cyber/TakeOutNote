# TakeOutNote & E-Commerce Shop

A full-stack portfolio project combining a cloud-based note-taking service and an e-commerce shop.
This project demonstrates user authentication, CRUD workflows, REST-style backend logic, database design, file uploads, and AWS deployment using PHP, MySQL, JavaScript, and AWS EC2/RDS.

> This project was uploaded with permission from **mindelevation**, and related intellectual property rights belong to **mindelevation**.
> For access to the server-side test key or additional setup information, please contact: **[rokbnoc@gmail.com](mailto:rokbnoc@gmail.com)**

---

## Overview

TakeOutNote integrates two related web services into one portfolio project:

### Cloud Note Service

A user-based note management system that allows users to create, edit, organize, and search notes.

Key features include:

* User authentication with sessions and JWT
* Create, read, update, and delete notes
* Folder-based note organization
* Tag-based search and filtering
* Image and file upload support
* User-specific access control

### E-Commerce Shop

A plain-PHP e-commerce web application that demonstrates product browsing, cart management, order creation, and admin-side management.

Key features include:

* Product listing and detail pages
* Category filtering and pagination
* Shopping cart add, update, and delete flows
* Mock checkout and order creation
* Order history lookup
* Admin product and order management

---

## Tech Stack

### Backend

* PHP
* MySQL
* REST-style API design
* PHP sessions
* JSON Web Token authentication

### Frontend

* HTML5
* CSS3
* Vanilla JavaScript

### Cloud & Deployment

* AWS EC2
* AWS RDS for MySQL
* Git
* GitHub

---

## Core Features

### Authentication & Authorization

* Implemented session-based authentication for web flows
* Added JWT-based user authentication for API-style access
* Enforced user-specific access control for notes, folders, and uploaded files

### Note Management

* Built full CRUD functionality for notes
* Added folder and tag management
* Supported image and file uploads up to 5MB
* Stored file metadata in MySQL and files in server-side directories

### E-Commerce Workflow

* Built product browsing, cart, order, and mock checkout flows
* Added admin-facing product and order management pages
* Designed relational database tables for users, products, carts, orders, notes, folders, tags, and uploads

### Database Design

* Designed and managed MySQL tables for both the note-taking and e-commerce services
* Structured relationships between users, notes, folders, tags, files, products, carts, and orders
* Supported data separation between different authenticated users

---

## Project Architecture

```text
TakeOutNote
├── note-service
│   ├── authentication
│   ├── notes
│   ├── folders
│   ├── tags
│   └── uploads
│
├── ecommerce-shop
│   ├── products
│   ├── cart
│   ├── orders
│   └── admin
│
├── database
│   └── MySQL schema and seed data
│
└── deployment
    └── AWS EC2 / RDS configuration
```

---

## Screenshots

> Add screenshots or GIFs here.

Recommended screenshots:

* Login page
* Cloud note dashboard
* Note editor
* Product listing page
* Cart page
* Admin dashboard

Example:

```md
![Cloud Note Dashboard](./screenshots/note-dashboard.png)
![E-Commerce Product Page](./screenshots/product-page.png)
```

---

## Setup Notes

This repository may not include all server-side keys or production configuration files for security and copyright reasons.

For local testing, you will need:

* PHP installed locally
* MySQL database
* Database credentials configured
* Required server-side keys or test access code

For access to test credentials or server-side setup instructions, please contact:

**[rokbnoc@gmail.com](mailto:rokbnoc@gmail.com)**

---

## Roadmap

Planned improvements include:

* Full-text search for notes
* Autocomplete for tags and search queries
* Improved admin dashboard UI
* Real-time statistics for admin users
* GitHub Actions based CI/CD pipeline
* Improved responsive design
* Progressive Web App support

---

## What I Learned

Through this project, I practiced:

* Building backend logic without relying on a full framework
* Designing relational database schemas for multiple service domains
* Implementing authentication and access control
* Connecting frontend flows with backend PHP logic
* Managing file uploads and database metadata
* Deploying a web application using AWS EC2 and RDS
* Structuring a portfolio project for maintainability and future expansion

---

## License & Copyright

* Code: MIT © 2025 Seongrok Lee
* Related project rights: © 2025 mindelevation. All rights reserved.
