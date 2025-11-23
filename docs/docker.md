Docker: iniciar/gestionar MySQL para desarrollo

1) Crear y ejecutar un contenedor MySQL con `docker run` (mapea puerto 3306 del contenedor a 3307 en el host):

```powershell
docker run --name meditrack-mysql -e MYSQL_ROOT_PASSWORD=rootpw -e MYSQL_DATABASE=MediTrack -e MYSQL_USER=meditrack_user -e MYSQL_PASSWORD=your_password -p 3307:3306 -d mysql:8.0
```

2) Usar `docker-compose` (archivo `docker-compose.yml` mínimo):

```yaml
version: '3.8'
services:
  db:
    image: mysql:8.0
    container_name: meditrack-mysql
    environment:
      MYSQL_ROOT_PASSWORD: rootpw
      MYSQL_DATABASE: MediTrack
      MYSQL_USER: meditrack_user
      MYSQL_PASSWORD: your_password
    ports:
      - "3307:3306"
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

Luego inicia con:

```powershell
docker-compose up -d
```

Cómo ver el ID del contenedor y detenerlo

- Lista contenedores en ejecución (verás `CONTAINER ID` y `NAMES`):

```powershell
docker ps
```

- Ejemplo de salida relevante:

```text
CONTAINER ID   IMAGE         COMMAND                  CREATED         STATUS         PORTS                    NAMES
a1b2c3d4e5f6   mysql:8.0     "docker-entrypoint.s…"   2 minutes ago   Up 2 minutes   0.0.0.0:3307->3306/tcp   meditrack-mysql
```

- Para detener el contenedor usa `CONTAINER ID` o `NAMES`:

```powershell
docker stop a1b2c3d4e5f6
# o
docker stop meditrack-mysql
```

- Para eliminar un contenedor detenido:

```powershell
docker rm meditrack-mysql
```

- Ver logs en vivo del contenedor:

```powershell
docker logs -f meditrack-mysql
```

- Si usaste `docker-compose` para levantarlo, para apagarlo y borrar la red/volúmenes creados por compose:

```powershell
docker-compose down
```

Notas:
- En Windows asegúrate de tener Docker Desktop corriendo.
- Si mapeas a `3307:3306` en el host, en `.env` de Laravel usa `DB_PORT=3307`.
- Puedes usar el nombre del contenedor (`meditrack-mysql`) en los comandos `docker stop`/`docker logs` para mayor comodidad.
