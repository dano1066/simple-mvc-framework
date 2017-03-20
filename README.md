# Simple Mvc Framework
This is a simple MVC framework that I have built for super light weight web application in PHP. I have aimed to mimmic the ASP.NET MVC4  method of routing, but there are aspects that are also similar to Laravel due to this being written in PHP. 

A route can be defined in multiple ways. The most standard way would be based on the catch all method of http://website.com/{controller}/{functionname}/{optionalid}. This catch all should cover all possible routes that you might need and this will work by default, no matter what you change in the settings. 

There are 2 settings in config.php that will allow you to define a static controller. In other words, this is the main controller that the site will default to. If you are making a simple enough site, one controller might be all you need so using this setting will be perfect. Even when enabled this setting doesnt mean that other controllers can't be used, but those controllers must have their name defined in the url. To access a function from your static controller, you can enable the options below. You will then be able to access a url without needing the controller name and it will default to the static controller and hit that method. For example http://website.com/contactus, where "contactus" is a method name in the static controller class.

```php
define("STATICCONTROLLER", TRUE);
define("STATICCONTROLLERNAME", "controllername");
```
The final way to define a route is explicitly. You may want to have a url such as http://example.com/contact-us. As you may no, PHP does not allow for a dash in the name of functions so this route would be impossible using the method above. You may also want to access a controller that is not the default status controller, but not want to provide the controller name in the url. You can do this by adding values to the routes.php file. The following will create a route that matches the url provided in this paragraph for contact-us.
```php
$routes[] = new RouteAlias("contact-us", "home", "contactus", $CacheEnabled);
```
To make the framework as efficient and quick as possible, I have built a caching framework directly into the core. In the config.php file, there are 2 constants that can be defined that control the cache. I would suggest that the cache is disabled during development, but once you are ready to go into production the following variables will enable the cache.
```php
define("ENABLECACHE", false);
define("CACHETTL", 600);
```
*Please note that your web server will need to have write access to the root of this directory in order to be able to write the html files to cache.
