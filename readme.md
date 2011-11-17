ExpiringHash

Small helper class to create a URL parameter containing a tamper-proof expiration date. 

This allows you to implement expiring URLs in the same way that Amazon S3 supports.

```php
// create
$eh = new ExpiringHash('my secret');
$hash = $eh->generate("15 minutes");
$url = "http://foo.com/mydownload?hash={$hash}";

// verify
$eh = new ExpiringHash('my secret');
$okToDownload = $eh->validate($_GET['hash']);
```
