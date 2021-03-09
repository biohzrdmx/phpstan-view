# phpstan-view

Simple viewer for PHPStan-generated JSON result files

## Usage

This was created for local usage, so it is assumed that you have a functional LAMP stack working.

Just place `phpstan-view.php` on your development root (that is, the folder where your repositories reside) and fire it in your browser.

By default the script lists all the folders as projects, when you click one it will search for a PHPStan JSON file using a preset folder structure. It may not fit your particular folder hierarchy, but you may modify it; just open `phpstan-view.php` on your favorite code editor and change the structure defined in `line 10`:

```php
    $json_location = 'tests/output/phpstan.json';
```

Use the correct folder structure that you use in your projects and save.

## License

This is provided under the MIT License.

Also, this is by no means related to PHPStan nor its authors.

Created by biohzrdmx.