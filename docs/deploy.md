# Deploy Esthetic Renovations

Server path: `/opt/edsthetic`  
Public URL: `https://edsthetic.co.za`  
NPM upstream: `127.0.0.1:8094`

## First boot

```bash
cd /opt
git clone https://github.com/vassago85/edsthetic.git edsthetic
cd /opt/edsthetic
cp .env.production.example .env
nano .env   # set APP_KEY, DB_PASSWORD, Mailgun, etc.
```

Generate an app key (on any machine with PHP, or after a one-off container):

```bash
# On the server, after .env exists:
docker run --rm -v "$PWD":/app -w /app php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)), PHP_EOL;"
# Paste that value into APP_KEY=
```

Build and start:

```bash
cd /opt/edsthetic
docker compose build --no-cache app
docker compose up -d
docker compose ps
curl -fsS http://127.0.0.1:8094/up
```

Create the first admin:

```bash
docker exec -it edsthetic-app php artisan admin:create \
  --name="Owner" \
  --email="info@edsthetic.co.za" \
  --password='choose-a-strong-password'
```

Nginx Proxy Manager: proxy host `edsthetic.co.za` → `http://127.0.0.1:8094`, force SSL.

## Rebuild after a code push

```bash
cd /opt/edsthetic && git pull origin main && docker compose build --no-cache app && docker compose up -d --force-recreate app scheduler queue
```

Always recreate **app**, **scheduler**, and **queue** together so they share the same `edsthetic-app:latest` image.

## Copy gallery media from Laragon (optional)

Photos are not in git. After prod is up, from your Windows machine (Git Bash / WSL):

```bash
# 1) Dump local DB (settings + projects + media rows)
pg_dump -h 127.0.0.1 -U postgres -d edsthetic -Fc -f edsthetic.dump

# 2) Pack media files
tar -czf edsthetic-media.tgz -C /c/laragon/www/edsthetic/storage/app public

# 3) Copy to server, then on the server:
docker compose stop app queue scheduler
docker exec -i edsthetic-pgsql pg_restore -U edsthetic -d edsthetic --clean --if-exists < edsthetic.dump
docker cp edsthetic-media.tgz edsthetic-app:/tmp/
docker exec edsthetic-app bash -c 'cd /var/www/html/storage/app && tar -xzf /tmp/edsthetic-media.tgz && chown -R www-data:www-data public'
docker compose up -d
```

Or skip the dump and re-upload photos in Filament admin on production.
