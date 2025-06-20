# 🎵 EntradasYA

Sistema de gestión de eventos, ventas y usuarios para boletería, desarrollado con **Laravel** (API backend) y **Vue.js** (SPA frontend).

## 📌 Características Principales

- Gestión de eventos, lugares y categorías.
- Venta y validación de entradas en tiempo real.
- Panel de administración para usuarios, pagos y estadísticas.
- API RESTful segura con Laravel Sanctum.
- SPA interactiva y rápida con Vue.js 3 y Vue Router.

---

## ⚙️ Tecnologías Usadas

### Backend (API)
- Laravel 10.x
- MySQL 
- Laravel Sanctum (Autenticación)
- Eloquent ORM

### Frontend (SPA)
- Vue.js 3
- Vue Router
- Axios
- Tailwind CSS

---

## 🚀 Cómo poner en marcha el proyecto

### 1. Clonar el repositorio

```bash
git clone https://github.com/tuusuario/EntradasYA.git
cd EntradasYA
```

### 2. Instala dependencias de PHP

```bash
composer install
```
### 3. Instala dependencias de Node.js

```bash
npm install
```

Configura tu base de datos en el archivo `.env` (host, usuario, contraseña, nombre de base de datos) (Si no está creado el archivo .env Ejecuta el comando ```copy .env.example .env``` para crear el archivo) (También puedes importar la base de datos de prueba dejada en la primera carpeta del proyecto llamada "BD" para importarla en phpmyadmin)

### 3. Configurar el frontend (Vue.js)

```bash
cd ../frontend
npm install
npm run dev
```

Esto levantará el cliente en `http://localhost:5173`

---

## Inicia el servidor de desarrollo

```bash
php artisan serve
```

---

## 🧪 Tests

Puedes ejecutar los tests del backend con:

```bash
php artisan test
```

## 👥 Autores

- Andrés González
- William Hernández
- José Álvarez
- Diego Alvarez
