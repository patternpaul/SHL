SHL
===========
This is the code repo for http://www.shl-wpg.ca/
 
How to hack on the SHL codebase
=======================
Run `composer install` from the src directory to install all your dependencies. You can run the PHPUnit test suite by running `./vendor/bin/phpunit`. There is a gulp file watcher that will auto run the test suite on file change. Run `npm install` from the src directory and then `gulp watch` to start the file watcher which will auto clean all config caches, twig cache, route caches and auto run your tests on file changes. You can also run `gulp watch-clean` instead to run all the clean steps and not run the tests.

Want to setup a local VM with all data from the live site?
===========================================================
Checkout the README in the local_setup directory.


