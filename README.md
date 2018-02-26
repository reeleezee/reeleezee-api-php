# reeleezee-api-php
This repository contains sample code for the Reeleezee API. These samples are explained at the [Reeleezee API Documentation ](https://www.reeleezee.nl/developer/docs/api/) site.

**Note**: the samples are designed for educational purposes and not per se for commercial implementation.

#### Dependencies
The samples have no additional dependencies, just make sure you have enabled the php_curl extension in your php.ini file.

    extension=php_curl[.dll/.so]


#### Getting Started
To run the sample programs you need to:

- Have a [Reeleezee administration](https://www.reeleezee.nl)
- Modify the settings.php file with your credential information and check the uri

```php
$username = 'username';
$password = 'password';
$uri = 'https://portal.reeleezee.nl/api/v1';
```

After that you're all set to run the samples.

