# CodeIgniter 4 Request Flow - বাংলা ব্যাখ্যা

## 🔄 সম্পূর্ণ Request Flow

### 1️⃣ URL থেকে Request আসে
```
http://chatpion2.test/
```

### 2️⃣ Apache .htaccess (public/.htaccess)
```apache
RewriteRule ^([\s\S]*)$ index.php/$1 [L,NC,QSA]
```
- সব request `public/index.php` এ redirect করে
- যদি file/directory না থাকে, তাহলে `index.php` এ পাঠায়

### 3️⃣ Front Controller (public/index.php)
```php
exit(Boot::bootWeb($paths));
```
- PHP version check করে
- Paths define করে
- Framework bootstrap করে
- `Boot::bootWeb()` call করে

### 4️⃣ Routes Configuration (app/Config/Routes.php)
```php
$routes->get('/', 'Home::index');
```
- URL `/` → `Home` controller এর `index()` method
- Route matching করে

### 5️⃣ Controller Loading (app/Controllers/Home.php)
```php
class Home extends BaseController
{
    public function index()
    {
        $display_landing_page = ($this->config->display_landing_page ?? null);
        
        if ($display_landing_page == '0')
            return $this->login_page();
        else 
            return $this->_site_viewcontroller();
    }
}
```

### 6️⃣ BaseController (app/Controllers/BaseController.php)
```php
public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
{
    // Session, Database, Config সব initialize হয়
    // CI3 compatibility layer setup হয়
}
```
- সব controller এর আগে `initController()` call হয়
- Session, Database, Config initialize হয়
- CI3 compatibility layer setup হয়

### 7️⃣ View Controller Method (_site_viewcontroller)
```php
public function _site_viewcontroller($data = array())
{
    // Database থেকে data fetch
    $data["pricing_table_data"] = $this->basic->get_data("package", ...);
    
    // Theme determine করে
    $body_load = "site/modern/index";
    
    // View return করে
    return view($body_load, $data);
}
```

### 8️⃣ View File (app/Views/site/modern/index.php)
```php
<!doctype html>
<html>
<head>
    <title><?php echo config('MyConfig')->product_name; ?></title>
</head>
<body>
    <!-- Landing page content -->
</body>
</html>
```

## 📁 Key Files এবং তাদের কাজ

### 1. public/index.php
- **কাজ**: Entry point, সব request এখানে আসে
- **Location**: `public/index.php`

### 2. public/.htaccess
- **কাজ**: URL rewrite, সব request `index.php` এ পাঠায়
- **Location**: `public/.htaccess`

### 3. app/Config/Routes.php
- **কাজ**: URL routing define করে
- **Example**: `$routes->get('/', 'Home::index');`
- **Location**: `app/Config/Routes.php`

### 4. app/Controllers/BaseController.php
- **কাজ**: Base controller, সব controller এটা extend করে
- **Features**: 
  - Session initialize
  - Database connection
  - CI3 compatibility layer
- **Location**: `app/Controllers/BaseController.php`

### 5. app/Controllers/Home.php
- **কাজ**: Main controller, landing page handle করে
- **Methods**:
  - `index()` - Main entry point
  - `login_page()` - Login page show করে
  - `_site_viewcontroller()` - Landing page show করে
- **Location**: `app/Controllers/Home.php`

### 6. app/Config/MyConfig.php
- **কাজ**: Custom configuration
- **Contains**: 
  - `display_landing_page`
  - `current_theme`
  - `product_name`
  - etc.
- **Location**: `app/Config/MyConfig.php`

### 7. app/Views/site/modern/index.php
- **কাজ**: Landing page view file
- **Location**: `app/Views/site/modern/index.php`

## 🔍 Step-by-Step Flow

```
1. Browser Request
   ↓
2. Apache Server (.htaccess)
   ↓
3. public/index.php (Front Controller)
   ↓
4. Boot::bootWeb() (Framework Bootstrap)
   ↓
5. Routes.php (Route Matching)
   ↓
6. Home::index() (Controller Method)
   ↓
7. BaseController::initController() (Initialization)
   ↓
8. Home::_site_viewcontroller() (View Controller)
   ↓
9. view('site/modern/index', $data) (View Rendering)
   ↓
10. app/Views/site/modern/index.php (View File)
   ↓
11. HTML Output (Browser এ display)
```

## 🎯 Important Concepts

### Routing
- URL → Controller Method mapping
- `Routes.php` এ define করা থাকে

### Controller
- Business logic handle করে
- Database query করে
- View এ data pass করে

### View
- HTML template
- Controller থেকে data receive করে
- User কে display করে

### Config
- Application settings
- `MyConfig.php` এ custom config
- `config('MyConfig')->property_name` দিয়ে access

### Session
- User session manage করে
- `$this->session->get()` / `$this->session->set()`

### Database
- `$this->basic->get_data()` - Data fetch
- `$this->db->table()->get()` - Query builder

## 💡 Example Flow

### Request: `http://chatpion2.test/`

1. **Apache** → `.htaccess` → `index.php`
2. **index.php** → `Boot::bootWeb()`
3. **Routes** →** `Home::index()`
4. **Home::index()** → Check `display_landing_page`
5. **If '1'** → `_site_viewcontroller()`
6. **_site_viewcontroller()** → Fetch data from database
7. **view()** → Load `site/modern/index.php`
8. **index.php (view)** → Render HTML
9. **Browser** → Display landing page

## 🔧 Configuration Files

- `app/Config/App.php` - Base URL, timezone, etc.
- `app/Config/Database.php` - Database connection
- `app/Config/Routes.php` - URL routing
- `app/Config/MyConfig.php` - Custom config
- `app/Config/Autoload.php` - Auto-load helpers, libraries

## 📝 Summary

**সহজ ভাষায়:**
1. Browser থেকে request আসে
2. Apache `.htaccess` দিয়ে `index.php` এ পাঠায়
3. `index.php` framework bootstrap করে
4. `Routes.php` check করে কোন controller call করতে হবে
5. Controller method execute হয়
6. Database থেকে data fetch হয়
7. View file load হয়
8. HTML output browser এ যায়

**Core Files:**
- `public/index.php` - Entry point
- `app/Config/Routes.php` - Routing
- `app/Controllers/Home.php` - Main controller
- `app/Views/site/modern/index.php` - View file

