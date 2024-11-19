
# Lightflow Back Office

Lightflow Back Office is Admin Panel With Preset of Roles, Permissions, ACL, User Management, Profile Management.

## Prerequisites
What things you need to install the software:

1. Node.js (i.e., >=v20.0.0)
2. PHP (i.e., >=8.2)
3. Laravel Framework (i.e., >=11.9)

Ensure these dependencies are installed and properly configured on your system before proceeding.

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

All Set ! now serve laravel app and vue app on local and open app in browser.

```bash
  php artisan serve
  npm run dev
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

## File Structure

Below is the working tree structure for the lightflow-back-office project:
```
lightflow-back-office
  |-- routes
    |-- all laravel routes files
  |-- public
    |-- images
    |   |-- all the image files
  |-- resources
    |-- views
    |   |-- app.blade.php
    |-- js
    |   |-- src
    |   |   |--assets
    |   |   |   |-- css
    |   |   |   |   |-- all the css files and folders
    |   |   |-- components
    |   |   |   |-- layout
    |   |   |   |   |-- all the vue js layout components
    |   |   |   |-- all the vue js other components
    |   |   |-- composables
    |   |   |   |-- all the vue js composables files
    |   |   |-- layout
    |   |   |   |-- all the app layouts
    |   |   |-- locales
    |   |   |   |-- all the locales json files
    |   |   |-- router
    |   |   |   |-- vuejs router file
    |   |   |-- store
    |   |   |   |-- vuejs store file
    |   |   |-- views
    |   |   |   |-- all the app pages
    |   |   |-- app-setting.ts - custom app setting configuration file
    |   |   |-- App.vue - vuejs project startup file
    |   |   |-- main.ts - vuejs entry point file
    |-- theme.config.ts
  |-- package.json
  |-- postcss.config.cjs
  |-- tailwind.config.cjs
  |-- tsconfig.json
  |-- tsconfig.node.json
  |-- vite.config.ts
  |-- .editorconfig
  |-- .gitignore
  |-- .prettierrc
```


## Code Structure

```
This section will give you a brief description of our code.
```
**1. Header Section :** This is the default navbar section. It contains :

    a. Sidebar Toggle button.

    b. Quick access button for calendar.

    c. Quick access button for todolist.

    d. Quick access button for chat.

    e. Search Bar

    f. Theme toggle button.

    g. Language Dropdown

    h. Message Dropdown

    i. Notification Dropdown

    j. User Profile with Dropdown

    k. Horizontal Menu
Note:- These categories are defined by us and you can modify as per your needs :)

```javascript
 <!--  Navbar Component  -->
  <Header></Header>
  ==========================================================

  <!--  BEGIN NAVBAR  -->
  <header :class="{ dark: store.semidark && store.menu === 'horizontal' }">
      <div class="shadow-sm">
          <div class="relative flex w-full items-center bg-white px-5 py-2.5 dark:bg-[#0e1726]">
              ..............................
          </div>

          <!-- horizontal menu -->
          <ul
              class="horizontal-menu hidden border-t border-[#ebedf2] bg-white py-1.5 px-6 font-semibold text-black rtl:space-x-reverse dark:border-[#191e3a] dark:bg-[#0e1726] dark:text-white-dark lg:space-x-1.5 xl:space-x-8"
          >
              ..............................
          </ul>
      </div>
  </header>
  <!--  END NAVBAR  -->
```

**2. Main Container Section :** The main container section includes header, footer and main content section.

    a. Sidebar Section

    b. Header Section

    c. Main Content Section

    d. Footer Section

```javascript
 <!--  BEGIN MAIN CONTAINER  -->
  <div
      class="main-section antialiased relative font-nunito text-sm font-normal"
      :class="[store.sidebar ? 'toggle-sidebar' : '', store.menu, store.layout, store.rtlClass]"
  >
      ..............................
  </div>
  <!-- END MAIN CONTAINER -->
```

**3. Sidebar :** This is the sidebar code.

```javascript
  <!--  Sidebar Component  -->
  <Sidebar></Sidebar>
  ==========================================================

  <!--  BEGIN SIDEBAR  -->
  <div :class="{ 'dark text-white-dark': store.semidark }">
      <nav class="sidebar fixed top-0 bottom-0 z-50 h-full min-h-screen w-[260px] shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300">
          ..............................
      </nav>
  </div>
  <!--  END SIDEBAR  -->
```

**4. Main Content :** This is the Main Content code section.

This is the root structure where you can create widgets, charts, tables etc.

```javascript
  <!--  BEGIN CONTENT PART  -->
  <div class="main-content">
      ..............................
  </div>

  <!--  END CONTENT PART  -->
```

**5. Footer :** This is the Footer code.

```javascript
   <!--  Footer Component  -->
  <Footer></Footer>
  ==========================================================

  <!--  BEGIN FOOTER  -->
  <p class="pt-6 text-center dark:text-white-dark ltr:sm:text-left rtl:sm:text-right">
      ..............................
  </p>
  <!--  END FOOTER  -->
```

### The Combined Code
Now, after a brief description of our admin template. Below is the combined code of the snippets we have discuss above.

```javascript
   <!--  BEGIN MAIN CONTAINER  -->
  <div class="main-section antialiased relative font-nunito text-sm font-normal" :class="[store.sidebar ? 'toggle-sidebar' : '', store.menu, store.layout, store.rtlClass]">
      <!-- BEGIN SIDEBAR MENU OVERLAY -->
      <div class="fixed inset-0 bg-[black]/60 z-50 lg:hidden" :class="{ hidden: !store.sidebar }" @click="store.toggleSidebar()"></div>
      <!-- END SIDEBAR MENU OVERLAY -->

      <!-- BEGIN SCREEN LOADER -->
      <div v-show="store.isShowMainLoader" class="screen_loader fixed inset-0 bg-[#fafafa] dark:bg-[#060818] z-[60] grid place-content-center animate__animated">
          <svg>...</svg>
      </div>
      <!-- END SCREEN LOADER -->

      <!-- BEGIN SCROLL TO TOP BUTTON -->
      <div class="fixed bottom-6 ltr:right-6 rtl:left-6 z-50">
          <template v-if="showTopButton">
              <button type="button" class="btn btn-outline-primary animate-pulse rounded-full bg-[#fafafa] p-2 dark:bg-[#060818] dark:hover:bg-primary" @click="goToTop">
                  <svg>...</svg>
              </button>
          </template>
      </div>
      <!-- END SCROLL TO TOP BUTTON -->

      <!-- BEGIN APP SETTING LAUNCHER -->
      <Setting />
      <!-- END APP SETTING LAUNCHER -->

      <div class="main-container text-black dark:text-white-dark min-h-screen" :class="[store.navbar]">
          <!--  BEGIN SIDEBAR  -->
          <Sidebar />
          <!--  END SIDEBAR  -->

          <!--  BEGIN CONTENT AREA  -->
          <div class="main-content">
              <!--  BEGIN NAVBAR  -->
              <Header></Header>
              <!--  END NAVBAR  -->

              <div class="p-6 animation">
                  <!--  BEGIN PAGE CONTENT  -->
                  <router-view></router-view>
                  <!--  END PAGE CONTENT  -->

                  <!-- BEGIN FOOTER -->
                  <Footer></Footer>
                  <!-- END FOOTER -->
              </div>
          </div>
          <!--  END CONTENT AREA  -->
      </div>
  </div>
  <!--  END MAIN CONTAINER  -->
```

## Feedback

If you have any feedback, please reach out to us at p.baboo@huulke.com


## License

© 2024 Huulke Technologies. All rights reserved.

Enjoy!