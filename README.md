
# Проект «Волонтёрка-backend» — пошаговый гайд по поднятию и использованию API

> Бэкенд для учёта бонусов волонтёров. Реализация на **Laravel 10 + Sanctum + PostgreSQL**  
> Документация API генерируется **Swagger / OpenAPI 3** (пакет `l5‑swagger`).

---

## 1. Предварительные требования

| ПО | Версия | Замечания |
|----|--------|-----------|
| PHP | ≥ 8.1 | расширения: `pdo_pgsql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `gd` |
| Composer | ≥ 2.5 | менеджер зависимостей PHP |
| PostgreSQL | ≥ 12 | база данных |
| Git | — | для клонирования репозитория |

---

## 2. Клонирование и установка

```bash
git clone https://github.com/Legacy-volonteer/backend.git
cd backend

# установка PHP‑зависимостей
composer install --no-dev --prefer-dist

# создаём локальную копию переменных окружения
cp .env.example .env
```

---

## 3. Настройка окружения

Откройте файл **.env** и задайте как минимум:

```dotenv
APP_NAME="Backy"
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=backy
DB_USERNAME=postgres
DB_PASSWORD=root
```

Затем сгенерируйте ключ приложения и выполните миграции с сидированием:

```bash
php artisan key:generate

# создаём структуру БД
php artisan migrate

# загружаем тестовые данные (компании, пользователи, бонусы, клеймы)
php artisan db:seed
```

> **Важно:** сидеры обнуляют (`truncate`) таблицы **users** и **volunteer_recipients** —
> не запускайте их на production.

---

## 4. Статические файлы и ссылки хранилища

```bash
php artisan storage:link   # если нужно отдавать файлы из storage/app/public
```

---

## 5. Генерация Swagger‑документации

```bash
php artisan l5-swagger:generate
```

После этого UI доступен по адресу  
`http://localhost:8000/api/documentation`

---

## 6. Запуск локального сервера

```bash
php artisan serve --port=8000
```

---

## 7. Тестовые учётные записи

| Роль | Логин | Пароль | Примечание |
|------|-------|--------|------------|
| **Администратор** | `admin` | `root` | Bearer‑токен не требуется |
| **Компания #1**  | `company@example.com` | `peresekin` | получает токен через `/api/company/login` |
| **Компания #2**  | `good_center@example.com` | `chooprin` | — |
| **Пользователь** | `user` | `root` | любой токен `/api/login` |

После авторизации Laravel Sanctum выдаёт Bearer‑токен (в JSON‑ответе `token`).

---

## 8. Основные конечные точки API

### 8.1. Аутентификация

| Метод | URL | Описание |
|-------|-----|----------|
| POST | `/api/register` | Регистрация волонтёра |
| POST | `/api/login` | Логин волонтёра |
| POST | `/api/user/logout` | Выход (требует токена) |

### 8.2. Пользовательские маршруты (`/api/user/*`)

* `GET  /profile` — текущий профиль  
* `GET  /bonuses` — активированные бонусы, отсортированы по `claimed_at DESC`  
* `GET  /bonuses/available` — свободные бонусы для пользователя  
* `GET  /bonuses/by-inn?inn` — бонусы по ИНН  
* `POST /bonuses/{bonus}/claim` — активировать бонус  
* `GET  /bonuses/history` — история всех полученных бонусов

### 8.3. Маршруты компаний (`/api/company/*`)

* `POST /register`, `POST /login`, `GET /me`  
* `GET  /volunteers`, `POST /volunteers/upload`  
* `POST /bonuses` — создать бонус  
* `GET  /bonuses`, `GET /bonuses/history`

### 8.4. Административные маршруты (`/api/admin/*`)

* `POST /volunteers/upload` — загрузка CSV‑файла (доступно без токена, проверка по логину‑`admin`)  
  *Формат CSV:* `full_name,inn,phone,email,birth_date(dd.mm.YYYY),achievements`
* `GET /volunteers` — все волонтёры  
* `GET /companies` — все компании  
* `GET /bonuses` — все бонусы

---

## 9. Пример работы API

```bash
# 1. получаем токен компании
curl -X POST http://localhost:8000/api/company/login      -H 'Content-Type: application/json'      -d '{"email":"company@example.com","password":"peresekin"}'

# 2. создаём бонус
curl -X POST http://localhost:8000/api/company/bonuses      -H "Authorization: Bearer <COMPANY_TOKEN>"      -H 'Content-Type: application/json'      -d '{"name":"Сертификат благодарности", "level":"min"}'
```

---


© 2025 — Legacy Team
