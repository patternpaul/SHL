Get your local environment setup
================================
1. Setup [Homestead](https://laravel.com/docs/5.3/homestead) (further Homestead instructions below)
2. Copy the contents of .env.example and create a .env file in the src directory.
3. run `composer install` from the src directory
4. ssh into Homestead (vagrant ssh from the homestead directory)
5. Navigate to the SHL src directory (~/Code/SHL/src)
6. run `artisan migrate` to create the SQL tables
7. run `artisan consumedata` to pull all data from the live site and add it to your database.
8. run `artisan refreshredis` to fire all events and set Redis.

Setup Homestead
===============
1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
2. Install [Vagrant](https://www.vagrantup.com/downloads.html)
3. Clone [Homestead](https://github.com/laravel/homestead) (git clone https://github.com/laravel/homestead.git)
4. run `bash init.sh` in the homestead repo. This will create a .homestead directory in your home directory
5. Configure the ~/.homestead/Homestead.yml file. Reference the `ExampleHomestead.yml` file. It's configured assuming that you've clone the SHL repo to ~/Code/SHL
6. run `vagrant up` from the root of the homestead repo to bring up the homestead VM
7. edit your hosts file (sudo vi /etc/hosts) and add `192.168.10.10   shl.app`. This will point requests to "shl.app" to your newly create virtual machine.