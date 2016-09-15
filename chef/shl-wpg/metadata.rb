name 'shl-wpg'
maintainer 'Paul Everton'
maintainer_email 'pattern.paul@gmail.com'
license 'no license. Just givr'
description 'Sets up box for shl-wpg'
long_description 'Sets up box for shl-wpg'
version '1.0.7'


depends 'users'
depends 'sudo'
depends 'yum', '~> 3.8.2'
depends 'yum-remi-chef', '~> 1.1.4'
depends 'os-hardening', '~> 1.3.1'
depends 'ssh-hardening', '~> 1.1.0'
depends 'mysql', '~> 5.6.3'
depends 'redisio', '~> 2.3.0'
depends 'firewall', '~> 2.5.0'
depends 'supervisor', '~> 0.4.12'