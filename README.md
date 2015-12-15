iTunes Connect Ingest
=======
[![Total Downloads](https://img.shields.io/packagist/dt/JayBizzle/itunes-connect-ingest.svg?style=flat-square)](https://packagist.org/packages/jaybizzle/itunes-connect-ingest) [![MIT](https://img.shields.io/badge/license-MIT-ff69b4.svg?style=flat-square)](https://github.com/JayBizzle/itunes-connect-ingest) [![Version](https://img.shields.io/packagist/v/jaybizzle/itunes-connect-ingest.svg?style=flat-square)](https://packagist.org/packages/jaybizzle/itunes-connect-ingest) [![StyleCI](https://styleci.io/repos/47654145/shield)](https://styleci.io/repos/32755917)

`composer require jaybizzle/itunes-connect-ingest`


#### Usage
```php
use Jaybizzle\ITCIngest;

$itc = new ITCIngest('itunesconnect@emailaddress.com', 'itun3sp455word', 'vendorid');
$itc->getData('20151207');
```

#### Where do I find my Vendor ID
See here - http://blog.exiconglobal.com/appstore-developerid-vendorid


