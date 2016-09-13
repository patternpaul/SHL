

default['nginx']['webroot'] = "/var/www"


default['yum']['remi6']['make_cache'] = false

default['show_phpinfo_as_index'] = true


default['mysql']['server_root_password'] = 'superhorsecow'
default['mysql']['mysqld_options']['sql-mode'] = 'TRADITIONAL'


default['authorization']['sudo']['groups'] = [ "sysadmin" ]
default['authorization']['sudo']['passwordless'] = true

default['ssh']['host_key_files'] = ['/etc/ssh/ssh_host_rsa_key', '/etc/ssh/ssh_host_dsa_key']



default['firewall']['allow_ssh'] = true
default['firewall']['firewalld']['permanent'] = true
default['shl-wpg']['open_ports'] = [80,443,10514]
default['shl-wpg']['local_open_ports'] = [3306,6379]
default['shl-wpg']['domains'] = 'shl-wpg.ca www.shl-wpg.ca'
default['shl-wpg']['secure'] = true