# Digital Direction — Тестовое задание

Готовое тестовое задание для **Digital Direction**.  
Стек: **Laravel 11**, **Sail (Docker Compose)**: PHP-FPM 8.2 + **Nginx** + **MySQL 8** + **phpMyAdmin**.  
Автор: **Сарыбай Адильхан**.

---

## Быстрый старт

```bash
# cоздайте/скопируйте файл .env
cp .env.example .env

# запуск
./vendor/bin/sail up -d

# для выполнения команд внутри контейнеров используйте
./vendor/bin/sail

# (пере)сгенерировать openapi.json
./vendor/bin/sail artisan l5-swagger:generate
```
### Адреса

Приложение: http://localhost/

phpMyAdmin: http://localhost:8081/
 (Host: mysql, логин/пароль — из .env)

OpenApi документация: http://localhost/api/documentation