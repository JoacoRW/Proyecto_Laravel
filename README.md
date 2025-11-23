## Requisitos mínimos

- PHP 8.2 o superior
- Composer v2+
## Instalación y puesta en marcha (PowerShell)

1. Clona el repo:

```powershell
git clone <repo-url>
cd Proyecto_Laravel
composer install
```

3. Copia `.env` y genera `APP_KEY`:


4. (SQLite) crea el archivo de BD y ajusta `.env`:

New-Item -Path . -Name "database\database.sqlite" -ItemType File -Force
# En .env: DB_CONNECTION=sqlite y DB_DATABASE=./database/database.sqlite
```

5. Ejecuta migraciones y seeders:

php artisan migrate
php artisan db:seed
# O limpiar y sembrar:
php artisan migrate:fresh --seed
```

6. Instala dependencias de Node y compila assets:
```powershell
npm install
npm run dev
```

7. Limpia cachés:

```powershell
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

8. Levanta el servidor de desarrollo:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
# Abrir: http://127.0.0.1:8000
```

---

## Rutas y endpoints importantes

- `GET /dashboard` — Dashboard (requiere auth)
- `GET|POST /consultas` — CRUD consultas (resource routes)
- `GET /db` — Inspector DB (auth)
- `GET /settings` — Página de configuración (auth)
- `GET /metrics/dashboard` — Endpoint JSON con métricas para los gráficos

Puedes ver todas las rutas con:

```powershell
php artisan route:list
```

---

## Tests

Ejecuta tests con:

```powershell
php artisan test
```

## Seeders y datos de ejemplo

El proyecto incluye seeders (por ejemplo `ConsultaSeeder`) que generan datos de prueba para `TipoConsulta`, `Consulta` y `ConsultaExamen`. Usa `php artisan db:seed` o `php artisan migrate:fresh --seed` para poblar la base.

### Generador de datos demo (Artisan)

Además del seeder tradicional, hay un comando Artisan que genera datos demo aleatorios y rellenará las tablas usadas por el dashboard: `generate:demo`.

Opciones y uso (PowerShell):

```powershell
# Ejecuta con valores por defecto:
php artisan generate:demo

# Personaliza la cantidad de datos (ejemplo):
php artisan generate:demo --patients=50 --consultas=200 --days=60 --exams=3

# Empezar limpio, luego generar:
php artisan migrate:fresh --seed
php artisan generate:demo --patients=50 --consultas=200 --days=60 --exams=3
```


## Usar `server.js` (Node API) en EC2

Si no quieres exponer la base de datos directamente, puedes ejecutar un microservicio Node (como `server.js` incluido en este repo) en la EC2 que actúe como API para que Flutter u otras apps consuman los datos.

Archivo: `server.js` (usa variables de entorno). Añade en la EC2 un `.env` con:

```
NODE_DB_HOST=127.0.0.1
NODE_DB_PORT=3306
NODE_DB_USER=meditrack_user
NODE_DB_PASS=M3d!Track2025
NODE_DB_NAME=MediTrack
PORT=3001
```

Instalación en la EC2 (ejemplo Ubuntu):

```bash
# instalar node
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs build-essential

# clonar o copiar el proyecto y colocar server.js y .env en el servidor
cd /path/to/project
npm install mysql2 express cors dotenv

# ejecutar (o usar pm2 para gestionarlo)
node server.js
# o con pm2:
npm install -g pm2
pm2 start server.js --name meditrack-node
pm2 save
```

Proxy y seguridad:
- Configura `nginx` como reverse proxy y obliga HTTPS (Let's Encrypt) en producción. No expongas el puerto 3001 directamente a Internet sin proxy.
- Habilita CORS y añade autenticación si la API estará pública.

En este repo hemos añadido además un proxy Laravel opcional que reenvía `/api/external/pacientes` a la API Node. Configura la URL en `.env`:

```
NODE_API_URL=https://api.example.com
```

Y usa las rutas API:
- `GET /api/external/pacientes`
- `GET /api/external/pacientes/{id}`


