.. Expose documentation master file, created by
   sphinx-quickstart on Sun Jun  9 07:11:27 2013.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

Expose - PHP Intrusion Detection
==================================

**Expose** is an Intrusion Detection System for PHP loosely based on the PHPIDS project (and using it's ruleset for detecting potential threats). You can find the latest version over on `it's github page <http://github.com/enygma/expose>`_.

**ALL CREDIT** for the rule set for Expose goes to the PHP IDS project. Expose literally uses the same JSON configuration for its execution. I am not claiming any kind of ownership or authorship of these rules. Please see the PHPIDS github README for names of those who have contributed.

Requirements
==============

Expose requires:

* PHP 5.3
* MongoDB support
* A MongoDB server to write to

The Mongo instance is used for two things - to write out the logging for the tool and, optionally, for 
use with the offline processing via the queue.

Sample Code
==================

The code below is a simple example of using Expose to handle the incoming data (``$data``) and process it against
the filter rules. It also shows some of the helper methods you can get to get the results of the 
filter run.

.. code-block:: php

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

Configuration
==================================

You can either specify the configuration for Expose as a set of array values or through an ``ini`` file on the
local filesystem.

To use an array, provide the `setConfig` method call with the values directly:

.. code-block:: php

    $manager = new \Expose\Manager($filters);
    $manager->setConfig(array(
        'queue_requests' => true
    ));

The above tells Expose to send all of the request data to the queue to be processed asyncronously via a cron job or something
running the backend CLI command. If you want to specify a file, you'd do it similarly but provide a path to a valid INI file rather than an array:

.. code-block:: php

    $manager = new \Expose\Manager($filters);
    $manager->setConfig('/path/to/config.ini');

The file doesn't have to be named ``config.ini`` - it'll use whatever path you give it and try to parse it as an INI-type file. If it fails, you'll get a lovely error message.

Loading the configuration also allows for the use of sub-settings, so you can use the "sections" feature of INI files to split out different settings.

Here's the current list of configuration options:

+---------------+----------------+
| Keyname       | Value          +
+===============+================+
| queue_requests| true/false     +
+---------------+----------------+
| notify.enable | true/false     |
+---------------+----------------+
| notify.type   | email          |
+---------------+----------------+
| notify.address| me@me.com      |
+---------------+----------------+

To set the values like those in the ``notify`` namespace, you use sub-values in arrays:

.. code-block:: php

    $manager = new \Expose\Manager($filters);
    $manager->setConfig(array(
        'notify' => array(
            'enable' => true
        )
    ));

or sections in the ``ini`` files:

.. code-block:: sh

    [nofity]
    enable=true

Real-time versus Queued Handling
==================================

Expose allows for two kinds of processing - real-time as the request comes in and delayed (queued). This can be controlled
with the ``queue_requests`` setting in the configuration. If it is set to true, Expose will take the request data and
insert it into the data store.

Real-time reporting will process the impact scores of the matching rules and report back the results. These results
can be fetched with the ``getReports`` method (as shown above). You're then free to do with the results as you wish.

Queued processing can be handled by something like a cron job using the command-line tool. When enabled, the request
data is pushed into the data store with a ``processed`` value of ``false``. The CLI then grabs the latest entries
from this queue and processes them against the rules. The results are either directly outputted in a JSON format
or can be written to an external file.

See the section on command line usage for more information.

Exceptions
==================

Expose lets you define two things to help with the evaluation of the data - exceptions and restrictions. Here's a definition of each:

Exceptions

An exception basically allows you to say "evaluate everything except this value". For example, to bypass the POSTed value of "foo" you would use:

.. code-block:: php

    $manager->setException('POST.foo');

This bypasses the value for that field and doesn't execute the filters on it.

Restrictions
==================
A restriction lets you tell Expose to only evaluate certain values and ignore all others. For example, we might have more data than we care around coming in and only want to check the value of POST.foo.bar:

.. code-block:: php

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

In this case, the filters would only run on ``POST.foo.bar`` and not on `POST.baz`.

Command Line
==============

Expose comes with a command-line tool to help make using the system simpler. You'll find it in the ``bin/``
directory inside of your installation. The CLI script inclues a few different commands:

* ``filter``
* ``process-queue``

Below are examples of how to use these commands.

Command Line - Filters
======================

The ``filter`` command gives you information about the filters loaded into the system. By default, it will
give you a list of the filters and their descriptions:

.. code-block:: sh
    
    bin/expose filter

The result is a list of IDs and the summaries from the filters, for example:

.. code-block:: sh
    
    1: finds html breaking injections including whitespace attacks
    2: finds attribute breaking injections including whitespace attacks
    3: finds unquoted attribute breaking injections
    4: Detects url-, name-, JSON, and referrer-contained payload attacks
    5: Detects hash-contained xss payload attacks, setter usage and property overloading
    6: Detects self contained xss via with(), common loops and regex to string conversion
    7: Detects JavaScript with(), ternary operators and XML predicate attacks

To get more information about a filter, use the ``id`` option:

.. code-block:: sh

    bin/expose filter --id=2

You'll be given the details about that filter:

.. code-block:: sh

    bin/expose --id=2

    [2] finds unquoted attribute breaking injections
        Rule: (?:^>[\w\s]*<\/?\w{2,}>)
        Tags: xss, csrf
        Impact: 2

Or, if you'd like information on more than one filter at a time, you can append
them with a comma:

.. code-block:: sh

    bin/expose --id=2,3

    [2] finds unquoted attribute breaking injections
        Rule: (?:^>[\w\s]*<\/?\w{2,}>)
        Tags: xss, csrf
        Impact: 2

    [3] Detects url-, name-, JSON, and referrer-contained payload attacks
            Rule: (?:[+\/]\s*name[\W\d]*[)+])|(?:;\W*url\s*=)|(?:[^\w\s\/?:>]\s*(?:location|referrer|name)\s*[^\/\w\s-])
            Tags: xss, csrf
            Impact: 5

Command Line - Queue
======================

The ``process-queue`` command lets you work with the queued request data. To use the queue processing, you
need to enable it with the ``queue_requests`` configuration option.

To process the current items in the queue, you can execute it without any command line options:

.. code-block:: sh

    bin/expose process-queue

This will provide you some messaging about how many items it will be processing (the default is 10 records
at a time) and output the resulting filter matches as JSON data.

If you'd like to output these results to a file instead, you can use the ``export-file`` option:

.. code-block:: sh

    bin/expose process-queue --export-file=/tmp/output.txt

This will apprend to the file if it already exists.

