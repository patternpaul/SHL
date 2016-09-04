# shl-wpg

Chef Cookbook to setup an SHL box. The current cookbook works with Centos 6.


Notes
======

Users
======
This cookbook will lock down ssh to ssh keys only. It also provides passwordless sudo to the "sysadmin" group. Ensure you have a "users" databag containing the users you wish to be able to SSH into the box.

```
{
  "id": "YOUR-USERNAME-HERE",
  "groups": [
    "sysadmin"
  ],
  "ssh_keys": [
    "YOUR-SSH-KEY-HERE"
  ]
}
```

MySQL Database
========
It's suggested to change the root password and app password once the box comes up.
```
UPDATE user SET Password=PASSWORD('NEW-ROOT-PASSWORD-HERE') WHERE User='root'; FLUSH PRIVILEGES; exit;
UPDATE user SET Password=PASSWORD('NEW-APP-PASSWORD-HERE') WHERE User='shlapp'; FLUSH PRIVILEGES; exit;
```


Redis Database
==============
Readers may notice that the Redis DB isn't locked down in any fashion. This could be changed. It only contains the materialized views of data and is dropped often. This could be changed in the future.