
# Lightflow Back Office

Lightflow Back Office is Admin Panel With Preset of Roles, Permissions, ACL, User Management, Profile Management.


## Installation

Install KimberLite With Simple Steps

```bash
  git clone https://github.com/rohan-huulke/lightflow-back-office.git
```
```bash
  cd lightflow-back-office
```

Install All Packages of laravel

```bash
  composer install
```

Install NPM Dependencies

```bash
  npm install
```

Create .env file by coping .env.example

```bash
  cp .env.example .env
```

Generate Application key

```bash
  php artisan key:generate
```

Update .env File with Database credentials and run migration with seed.

```bash
  php artisan migrate --seed
```

All Set ! now serve laravel app on local and open app in browser.

```bash
  php artisan serve
```

Login With Admin

```bash
  Username - admin@admin.com
  Password - Admin@123#
```

## Technologies used
Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.
 
[Vue.js (3)](https://vuejs.org/) as framework for development.

[Vue Router](https://router.vuejs.org/) for handling routes.

[Tailwind CSS](https://tailwindcss.com/docs/installation) Tailwind CSS works by scanning all of your HTML files, JavaScript components, and any other templates for class names, generating the corresponding styles and then writing them to a static CSS file.
## Feedback

If you have any feedback, please reach out to us at p.baboo@huulke.com


## License

© 2024 Huulke Technologies. All rights reserved.

Enjoy!