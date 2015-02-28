# TrackMy.Services
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/volodymyr-bondarenko/TrackMy.Services/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/volodymyr-bondarenko/TrackMy.Services/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/volodymyr-bondarenko/TrackMy.Services/badges/build.png?b=master)](https://scrutinizer-ci.com/g/volodymyr-bondarenko/TrackMy.Services/build-status/master)
[![GitHub release](https://img.shields.io/github/release/volodymyr-bondarenko/TrackMy.Services.svg?style=flat)](https://github.com/clickalicious/phpMemAdmin/releases) 
[![Packagist](https://img.shields.io/packagist/l/clickalicious/phpmemadmin.svg?style=flat)](http://opensource.org/licenses/BSD-3-Clause)

![Logo of TrackMy.Services](docs/logo_large.png)

## About
[TrackMy.Services] - easy tool to track all your sites and servers online. Install app on your smartphone and get notification messages each time, when щт your server or web site appear a problem.

## Features

 - Server load avaraige;
 - Tracking sevices on the server;
 - Disks usage.

## Platforms

Linux, Unix.

## Installation

Copy the file `server_info.php` to the availeble from Internet folder on the server and add to app link:

```php
http://yourdomain.com/your_path/server_info.php
```
## Configuration

Add services, which you want to track and users, that launch them:
```php
// add your services to track here
$servicesToCheck=array();
$servicesToCheck[]=array('what'=>'nginx', 'user'=>'www-data');
$servicesToCheck[]=array('what'=>'apache2', 'user'=>'www-data');
$servicesToCheck[]=array('what'=>'mysql', 'user'=>'mysql');
$servicesToCheck[]=array('what'=>'fail2ban', 'user'=>'root');
```

## Security
You can block direct access to the statistic via providing password to `server_info.php`:
```php
// set up password
$password=false; //without password (default)
$password='p@$$w0rd'; //data is not available without password
```


## Todo's

 - Fix status for serives in FreeBSD.





[TrackMy.Services]:http://trackmy.services/