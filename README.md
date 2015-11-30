## Emu Client

This package will work independently of the Laravel Framework.

### Composer Setup
```json
{
    "require": {
        "imamuseum/emu-client": "dev-master@dev"
    },
    "repositories": [
        {
            "type": "git",
            "url": "https://bitbucket.org/imalab/emu-client.git"
        }
    ]
}
```

### Basic Usage
```php
use Imamuseum\EmuClient\EmuExport;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require_once 'path/to/config/emu-client.php';

$emu = new EmuExport($config);
$emu->saveJsonFiles();


```

## Laravel Usage

### Service Provider
In `config\app.php` add to the autoloaded providers -
```php
Imamuseum\EmuClient\EmuClientServiceProvider::class,
```
### Publish Config
```php
php artisan vendor:publish
```
Now you can set-up your emu-client in `config\emu-client.php`.

### Environmental Variables
Add to your `.env`:
```
EMU_HOST=hostname
EMU_PORT=40000
```

### Artisan Command
```sh
php artisan emu:export
```
Will output chunked json files to the directory defined as your `export_path`.
