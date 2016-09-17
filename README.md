SHL
===========
This is the code repo for http://www.shl-wpg.ca/

What is SHL?
==============
SHL (Sunday Hockey League) is a Winnipeg ballhockey league. The core players are a collection of highschool friends in Winnipeg who have been playing ballhockey most sundays for 15 seasons. This site contains those 15 seasons of data.

The site has gone through many iterations. The previous iteration has been archived under the release_v2_backup branch. It's terrible code but it's been kept around for nostalgia sakes.

The SHL site is built off a CQRS (Command Query Responsibility Segregation) and ES (Event Sourced) architecture. It's hand rolled so you probably will find some turds. One of the interesting bits in the code base is that it has intelligent cache bust/refresh logic based off events flowing through the system. It assumes the site is locally cached with nginx.

Architecture
============
It's a home rolled CQRS (Command Query Responsibility Segregation) and ES (Event Sourced) architecture.
![architecture](https://raw.githubusercontent.com/patternpaul/SHL/master/SHL.png "CQRS+ES")

In this implementation, the commands are self handling commands using Laravel's synchronous [queues](https://laravel.com/docs/5.2/queues). Aggregates contain specific domain business rules. They will then generate events which will be stored locally and sent over an event bus. Event listeners use Laravel's [event subscription](https://laravel.com/docs/5.2/events#event-subscribers) logic to consume the events it cares about. Listeners will then consume those events and store them in a de-normalized state optimized for a given query (think materialized views). The same Listener objects will be used to answer specific queries that it has materialized. 

 
How to hack on the SHL codebase
=======================
Run `composer install` from the src directory to install all your dependencies. You can run the PHPUnit test suite by running `./vendor/bin/phpunit`. There is a gulp file watcher that will auto run the test suite on file change. Run `npm install` from the src directory and then `gulp watch` to start the file watcher which will auto clean all config caches, twig cache, route caches and auto run your tests on file changes. You can also run `gulp watch-clean` instead to run all the clean steps and not run the tests.

Want to setup a local VM with all data from the live site?
===========================================================
Checkout the README in the local_setup directory.

Want to setup your own box?
===========================
There is a Chef cookbook under the chef directory.

TODO
====
-Season Selector on season page
-Hint overlays for table headers
-API Cache Bust



