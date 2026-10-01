# Purchase Requests

Малък модул за създаване и управление на заявки за покупка, разработен с Yii2.

## Requirements

- PHP 8+
- MySQL / MariaDB
- Composer

## Installation

Clone the repository:

```bash
git clone <repository-url>
cd purchase-requests
```

Install dependencies:

```bash
composer install
```

Create a MySQL database:

```sql
CREATE DATABASE purchase_requests
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Configure the database connection in:

```text
config/db.php
```

Example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=purchase_requests',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
];
```

Run the migrations:

```bash
php yii migrate
```

Start the application:

```bash
php yii serve
```

The application should now be available at:

```text
http://localhost:8080
```

## Demo users

Използвани са стандартните потребители от Yii2 Basic шаблона.

Мениджър:

```text
Username: admin
Password: admin
```

Служител:

```text
Username: demo
Password: demo
```

`demo` се използва като обикновен служител и може да създава и изпраща заявки за одобрение.

`admin` се използва като мениджър и може да одобрява или отказва изпратените заявки.

## Как работи

Служителят създава нова заявка за покупка със заглавие, описание и сума.

Новата заявка първоначално се създава със статус:

```text
draft
```

Докато заявката е `draft`, нейният създател може да я редактира.

След като е готова, заявката може да бъде изпратена за одобрение и преминава в:

```text
draft -> pending
```

Мениджърът вижда изпратените заявки и може да ги одобри или откаже:

```text
pending -> approved
pending -> rejected
```

Други промени на статусите не са разрешени.

При отказ е задължително да бъде въведен коментар.

Потребител не може да одобри собствената си заявка.

Всяка промяна на статуса се записва в историята на заявката. Запазват се потребителят, старият статус, новият статус, коментарът и времето на промяната.

Промяната на статуса и записът в историята се извършват в една database transaction.

## API

Добавен е прост endpoint за извличане на всички заявки със статус `pending`:

```text
GET /api/purchase-requests/pending
```

Резултатът се връща в JSON формат.