Expose: an IDS for PHP
=========================

[![Build Status](https://secure.travis-ci.org/enygma/expose.png?branch=master)](http://travis-ci.org/enygma/expose)

Expose is an IDS for PHP loosely based on the PHPIDS project (and using it's ruleset
for detecting potential threats).

**ALL CREDIT** for the rule set for Expose goes to the PHP IDS project. Expose literally
uses the same JSON configuration for its execution. I am not claiming any kind of ownership
or authorship of these rules. Please see [the PHPIDS github README](https://github.com/PHPIDS/PHPIDS)
for names of those who have contributed.

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

// get all matching filter reports
$reports = $manager->getReports();
print_r($reports);

// export out the report in the given format ("text" is default)
echo $manager->export();
echo "\n\n";
?>
```