
# 📦 Инструкция по запуску проекта «Backy»

> Документ предназначен для локального развёртывания на **Windows / macOS / Linux**.  
> Ниже приведены минимальные шаги: установка зависимостей, настройка окружения, миграции/сиды и запуск.

---

## 1. Системные требования

| Компонент | Минимальная версия |
|-----------|-------------------|
| PHP       | **8.1** (расширения: `mbstring`, `openssl`, `pdo_pgsql`, `curl`, `gd`) |
| Composer  | **2.x** |
| PostgreSQL| **12** |
| Node.js   | **18** (нужно только для сборки front‑end ассетов) |

## 2. Клонирование репозитория

```bash
git clone https://github.com/your-org/backy.git
cd backy
```

## 3. Установка PHP‑зависимостей

```bash
composer install --no-interaction --prefer-dist
```

## 4. Настройка `.env`

```bash
cp .env.example .env
```

Заполните параметры БД и приложения (пример):

```dotenv
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=backy
DB_USERNAME=postgres
DB_PASSWORD=postgres

SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost
```

Сгенерируйте ключ приложения:

```bash
php artisan key:generate
```

## 5. Миграции и сиды

> ⚠️ Команда удалит старые данные, используйте на чистой БД.

```bash
php artisan migrate:fresh --seed
```

### Что создаётся в результате сидирования

| Сидер                 | Данные                                                              |
|-----------------------|---------------------------------------------------------------------|
| `CompanySeeder`       | 2 компании 				                              |
| `UserSeeder`          | 10 волонтёров из CSV + системный **user / root**                    |
| `BonusSeeder`         | По 2‑3 бонуса на каждую компанию                                    |
| `BonusClaimSeeder`    | Пара использованных бонусов, чтобы в истории было что показать      |

## 6. Запуск сервера

```bash
php artisan serve
# http://localhost:8000
```


## 7. Swagger‑документация

После запуска откройте:

```
http://localhost:8000/api/documentation
```

Авторизация выполняется через Bearer‑токен.

## 8. Тестовые учётные записи

| Роль          | Логин / Email               | Пароль |
|---------------|-----------------------------|--------|
| **Admin**     | `admin`                     | `root` |
| **User**      | `user` / `user@example.com` | `root` |
| **Company 1** | `help_fund@example.com`     | `peresekin` |
| **Company 2** | `good_center@example.com`   | `chooprin`  |

---

### Полезные Artisan‑команды

| Команда                              | Описание                                   |
|--------------------------------------|--------------------------------------------|
| `php artisan route:list`             | показать все маршруты                      |
| `php artisan l5-swagger:generate`    | пересоздать OpenAPI‑спецификацию           |