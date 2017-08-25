<?php

$config['DB_HOST'] = 'localhost';
$config['DB_USERNAME'] = 'root';
$config['DB_PASS'] = 'password';
$config['DB_NAME'] = 'moo6';
$config['DB_DRIVER'] = 'mysql';
$config['BASE_URL'] = 'localhost:8084';
$config['EMAIL_URL'] = 'localhost:8084';
$config['SITE_NAME_TXT'] = 'my site name';
$config['SALT'] = '4b3Zy8mW5OYBkk0w8VwQnmLySrd5P9lscqZicAq7VQnj6uMPvtMdX697E0haD8GIFFleXp0T7JWGWJ3RvHDIJ2fexON2LIMYczjNiaf9BzgVRhHhuTPFXtUpoDGvuBz2k1xKmonCH1RQYoUx1ElgE3';
$config['ADMIN_THEME'] = 'localhost:8084/theme/admin';
$config['RSS_FEED'] = 'true';


foreach ($config as $name => $value) {
    define($name,$value);
}
