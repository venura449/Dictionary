# Dictionary
Madura Project

## Authentication

- New pages: `Pages/yomupath/login.php`, `Pages/yomupath/register.php`
- API: `Pages/yomupath/auth_api.php` with actions `login`, `register`, `logout`, `me`
- Helpers: `Config/auth.php` (session-based). Admin emails are hardcoded; edit the `$ADMIN_EMAILS` list.
- User model: `Config/User.php`

### Database: users table
- Assets

The site logo is at `assets/logo.svg`. Replace it with your own SVG or use a PNG:

```html
<img src="../../assets/logo.png" alt="Logo" style="width:28px;height:28px;display:block;" />
```


Create a `users` table:

```sql
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(190) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

All registered accounts are regular users. Admins are determined solely by email in `Config/auth.php`.

### Permissions

- Non-admin users: view/search/filter only (UI hides Add/Edit/Delete). Server rejects write actions.
- Admin users (by hardcoded email): full access to Add/Edit/Delete via APIs.