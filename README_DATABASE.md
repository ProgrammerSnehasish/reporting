# Database Configuration & Schema Sync Guide

## 1. Connection Overview
The project is connected to **NeonDB (PostgreSQL)** using credentials stored in [`.env`](file:///.env).

- **Host**: `ep-lingering-shape-b302s22g-pooler.c-4.ap-southeast-1.aws.neon.tech`
- **Port**: `5432`
- **Database**: `neondb`
- **SSL**: `require`
- **Config File**: [`config/database.php`](file:///config/database.php)

---

## 2. Managing Future Schema Updates (Tables & Fields)

Whenever you add new tables or modify fields in [`schema.prisma`](file:///schema.prisma), you can synchronize with NeonDB using these commands:

### A. Push Schema Changes to NeonDB
To apply new models, columns, or relations directly to your NeonDB database:
```bash
npx prisma db push
# OR
npm run db:push
```

### B. Pull Existing Database Schema into `schema.prisma`
If you make changes directly inside NeonDB console and want to update `schema.prisma`:
```bash
npx prisma db pull
# OR
npm run db:pull
```

### C. Open Visual Web Database Browser (Prisma Studio)
To view, edit, and filter your NeonDB data in a clean web UI:
```bash
npx prisma studio
# OR
npm run db:studio
```

---

## 3. PHP Usage Example
In your PHP scripts, [`config/database.php`](file:///config/database.php) provides both `$pdo` and helper functions:

```php
require_once __DIR__ . '/config/database.php';

// Fetch multiple rows
$doctors = db_fetch_all("SELECT * FROM tbl_doctors WHERE status = :status", ['status' => 1]);

// Fetch single row
$user = db_fetch_one("SELECT * FROM tbl_users WHERE username = :u LIMIT 1", ['u' => 'admin']);

// Direct PDO Query
$stmt = $pdo->prepare("INSERT INTO tbl_notices (title, message) VALUES (:title, :msg)");
$stmt->execute(['title' => 'Meeting', 'msg' => 'Team meet at 4 PM']);
```
