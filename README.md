# Laravel Repository Pattern Generator

A lightweight Laravel package to generate repository and interface files with automatic binding and provider registration.

---

## 📦 Installation

Install the package via Composer:

```bash
composer require erfanwd/laravel-repository-pattern
```

## Usage

publish provider using:

```bash
php artisan vendor:publish --tag=repository-base
```

then simply:
```bash
php artisan make:repository ModelName
```