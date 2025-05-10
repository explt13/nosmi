# Nosmi PHP Framework

**Nosmi** is a minimal and lightweight PHP framework designed for rapid development and prototyping of small to medium-scale web applications.

---

## 🚀 Features

- Clean and simple project structure  
- Follows the MVC (Model-View-Controller) pattern  
- Minimal dependencies  
- Easy to understand and extend  
- Great for learning or building small apps quickly

---

## 📦 Installation

You can install Nosmi via Composer:

```bash
composer create-project explt13/nosmi my-app
```

Installation via Script
1. Download and install nosmi from this repository.
2. Add it to your system's PATH.
3. Run the following command:
```bash
nosmi init
```
This will initialize the project structure and install the required dependencies.

---
## 🗂 Project Structure

my-app/\
├── bin/ \
├── public/ \
&emsp;&emsp;&ensp;├── index.php \
&emsp;&emsp;&ensp;├── .htaccess \
&emsp;&emsp;&ensp;└── ... \
├── src/ \
&emsp;&emsp;&ensp;├── models \
&emsp;&emsp;&ensp;├── controllers \
&emsp;&emsp;&ensp;├── render \
&emsp;&emsp;&ensp;&emsp;&emsp;&ensp;├── views \
&emsp;&emsp;&ensp;&emsp;&emsp;&ensp;&emsp;&emsp;&ensp;├── errors \
&emsp;&emsp;&ensp;&emsp;&emsp;&ensp;&emsp;&emsp;&ensp;└── ... \
&emsp;&emsp;&ensp;&emsp;&emsp;&ensp;└── layouts \
&emsp;&emsp;&ensp;└── ... \
├── tests/ \
├── composer.json \
└── ...

---
## 🧱 Basic Usage

```
public/index.php
require_once __DIR__ . '/../vendor/autoload.php';
use Nosmi\App;
$app = (new App())
        ->bootstrap(config_path)
        ->run();
```
---
## 📄 License

This project is licensed under the MIT License.
