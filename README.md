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

### Exceptions & Restrictions

Expose lets you define two things o help with the evaluation of the data - *exceptions*
and *restrictions*. Here's a definition of each:

#### Exceptions

An exception basically allows you to say "evaluate everything except this value". For 
example, to bypass the `POST`ed value of "foo" you would use:

```php
<?php
$manager->setException('POST.foo');
?>
```

This bypasses the value for that field and doesn't execute the filters on it.

#### Restrictions

A restriction lets you tell Expose to only evaluate certain values and ignore all others.
For example, we might have more data than we care around coming in and only want to 
check the value of `POST.foo.bar`:

```php
<?php

$data = array(
    'POST' => array(
        'foo' => array(
            'bar' => 'test one'
        ),
        'baz' => 'test two'
    )
);

$filters = new \Expose\FilterCollection();
$filters->load();

$manager = new \Expose\Manager($filters);
$manager->setRestriction('POST.foo.bar');
$manager->run($data);
?>
```

In this case, the filters would only run on `POST.foo.bar` and *not* on `POST.baz`.

#### Using the Command-line

Also included is a command line tool that can give you more information about the filters 
in the system. Here's an example of its use:

##### Return the full list of filters
```sh
bin/expose filter
```

##### Return the details on one filter
````sh
bin/expose filter --id=37
```
