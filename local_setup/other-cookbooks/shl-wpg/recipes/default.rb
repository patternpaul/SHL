#
# Cookbook Name:: shl-wpg
# Recipe:: default
#
# Copyright (c) 2015 The Authors, All Rights Reserved.


include_recipe 'yum::default'
include_recipe 'yum-remi-chef::remi'
include_recipe 'yum-remi-chef::remi-php70'
include_recipe 'yum::default'


package 'git' do
  action :upgrade
end


# Stuff for net data

package 'zlib-devel' do
  action :upgrade
end
package 'gcc' do
  action :upgrade
end
package 'make' do
  action :upgrade
end
package 'autoconf' do
  action :upgrade
end
package 'automake' do
  action :upgrade
end
package 'pkgconfig' do
  action :upgrade
end

package 'php' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end
package 'php-fpm' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end


package 'php-mbstring' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end



package 'php-gd' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end

package 'php-mcrypt' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end

package 'php-pdo' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end

package 'php-mysql' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end

package 'composer' do
  options "--enablerepo=remi-php70 --enablerepo=remi"
  action :upgrade
end

package 'epel-release' do
  action :upgrade
end


yum_repository 'new-nginx-repo' do
  description 'Extra Packages for Enterprise Linux'
  baseurl 'http://nginx.org/packages/centos/6/$basearch/'
  gpgcheck false
  action :create
end

package 'nginx' do
  action :upgrade
end



directory '/usr/share/nginx/html/blue/public' do
  owner 'nginx'
  group 'nginx'
  mode '0755'
  recursive true
  action :create
end

directory '/usr/share/nginx/html/green/public' do
  owner 'nginx'
  group 'nginx'
  mode '0755'
  recursive true
  action :create
end


directory '/usr/share/nginx' do
  owner 'nginx'
  group 'nginx'
  mode '0755'
  recursive true
  action :create
end


link '/usr/share/prd-app' do
  to '/usr/share/nginx/html/blue'
  not_if { File.symlink?('/usr/share/prd-app') }
end

link '/usr/share/nginx/html/prd' do
  to '/usr/share/nginx/html/blue/public'
  not_if { File.symlink?('/usr/share/nginx/html/prd') }
end

link '/usr/share/nginx/html/stg' do
  to '/usr/share/nginx/html/green/public'
  not_if { File.symlink?('/usr/share/nginx/html/stg') }
end


# create php test file in the webroot
template '/usr/share/nginx/html/blue/public/test-page.php' do
  source 'php-test-1.erb'
  mode 00755
  only_if { node['show_phpinfo_as_index'] }
end

template '/usr/share/nginx/html/green/public/test-page.php' do
  source 'php-test-2.erb'
  mode 00755
  only_if { node['show_phpinfo_as_index'] }
end

# create nginx sites-available
directory '/etc/nginx/sites-available' do
  owner 'root'
  group 'root'
  mode '0755'
  action :create
end

# create nginx sites-available
directory '/etc/nginx/sites-enabled' do
  owner 'root'
  group 'root'
  mode '0755'
  action :create
end


# create nginx server block file
template '/etc/nginx/nginx.conf' do
  source 'nginx.conf.erb'
  owner 'root'
  group 'root'
  mode 00755
end

# create nginx server block file
template '/etc/nginx/sites-available/default' do
  source 'sites-available-default.erb'
  owner 'root'
  group 'root'
  mode 00755
  variables( :domains => node['shl-wpg']['domains'] )
end

link '/etc/nginx/sites-enabled/default' do
  to '/etc/nginx/sites-available/default'
end


#make this a variable
file '/var/run/php-fpm/php5-fpm.sock' do
  mode '0660'
  owner 'nginx'
  group 'nginx'
  action :touch
end



template '/etc/php-fpm.d/www.conf' do
  source 'www.conf.erb'
  owner 'root'
  group 'root'
  mode 00644
end

template '/etc/php.ini' do
  source 'php.ini.erb'
  owner 'root'
  group 'root'
  mode 00644
end

template '/etc/hosts' do
  source 'hosts.erb'
  owner 'root'
  group 'root'
  mode 00644
end


include_recipe 'mysql::server'
include_recipe 'mysql::client'

include_recipe 'redisio::default'
include_recipe 'redisio::enable'



template '/root/.bashrc' do
  source 'bashrc.erb'
  owner 'root'
  group 'root'
  mode 00644
end

# TODO: This shit should not be here


bash 'setup_db' do
  code <<-EOH
    mysql --user="root" --password="#{node['mysql']['server_root_password']}" --execute="CREATE USER 'shlapp'@'localhost' IDENTIFIED BY 'RANDOMSECRET'; GRANT ALL PRIVILEGES ON shl.* TO 'shlapp'@'localhost'; CREATE DATABASE shl;"
    touch dbcreated
    EOH
  not_if { ::File.exists?('./dbcreated') }
end



include_recipe 'supervisor::default'
supervisor_service "artisan_job" do
  action [ :enable, :restart ]
  process_name '%(program_name)s_%(process_num)02d'
  command 'php /usr/share/prd-app/artisan queue:listen --timeout=60 --queue=purgeurl --sleep=3 --tries=3'
  autostart true
  autorestart true
  user 'root'
  numprocs 2
  redirect_stderr true
end

service 'php-fpm' do
  action [ :enable, :restart ]
end

service 'nginx' do
  action [ :enable, :restart ]
end


template '/etc/rsyslog.conf' do
  source 'rsyslog.conf.erb'
  owner 'root'
  group 'root'
  mode 00644
end

remote_file 'Set Time To Central' do
  path '/etc/localtime'
  source 'file:///usr/share/zoneinfo/Canada/Central'
  owner 'root'
  group 'root'
  mode 0644
end
template '/etc/sysconfig/clock' do
  source 'clock.erb'
  owner 'root'
  group 'root'
  mode 00644
end

service 'rsyslog' do
  action [ :enable, :restart ]
end


if node['shl-wpg']['secure']
  include_recipe 'users::sysadmins'
  include_recipe 'firewall::default'
  include_recipe 'sudo::default'


  ports = node['shl-wpg']['open_ports']
  firewall_rule "open ports #{ports}" do
    port ports
  end

  ports = node['shl-wpg']['local_open_ports']
  firewall_rule "local open ports #{ports}" do
    port ports
    source '127.0.0.1'
  end

  service 'iptables' do
    action [ :enable, :restart ]
  end


  include_recipe 'ssh-hardening::default'
  include_recipe 'ssh-hardening::unlock'
  include_recipe 'os-hardening::default'

end


