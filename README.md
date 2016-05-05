# php-ldap
LDAP management and utils for PHP


# Known issues

If you get "Strong(er) authentication required" error, put this line before your script:

```php
putenv('LDAPTLS_REQCERT=never');
```

And pass $startTls as TRUE:

```php
public function __construct($host, $port, $user = null, $pass = null, $startTls = false)
```
