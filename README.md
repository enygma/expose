Expose: an IDS for PHP
=========================

Expose is an IDS for PHP loosely based on the PHPIDS project (and using it's ruleset
for detecting potential threats).

**Example usage:**

```php
<?php

$data = array(
    'POST' => array(
        'test' => 'foo',
        'bar' => array(
            'baz' => 'quux',
            'testing' => '<script>test</script>'
        )
    )
);

$filters = new \Expose\FilterCollection();
$filters->load();

$manager = new \Expose\Manager($filters);
$manager->run($data);

echo 'impact: '.$manager->getImpact()."\n"; // should return 8

?>
```