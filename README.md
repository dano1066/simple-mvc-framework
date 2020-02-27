# Simple Mvc Framework
This is a simple MVC framework that I have built for super light weight web application in PHP. I have aimed to mimmic the ASP.NET MVC4  method of routing, but there are aspects that are also similar to Laravel due to this being written in PHP. 

## Routes
A route can be defined in multiple ways. The most standard way would be based on the catch all method of website.com/{controller}/{functionname}/{optionalid}. This catch all should cover all possible routes that you might need and this will work by default, no matter what you change in the settings. 

There are 2 settings in config.php that will allow you to define a static controller. In other words, this is the main controller that the site will default to. If you are making a simple enough site, one controller might be all you need so using this setting will be perfect. Even when enabled this setting doesnt mean that other controllers can't be used, but those controllers must have their name defined in the url. To access a function from your static controller, you can enable the options below. You will then be able to access a url without needing the controller name and it will default to the static controller and hit that method. For example website.com/contactus, where "contactus" is a method name in the static controller class.

```php
define("STATICCONTROLLER", TRUE);
define("STATICCONTROLLERNAME", "controllername");
```
The final way to define a route is explicitly. You may want to have a url such as example.com/contact-us. As you may no, PHP does not allow for a dash in the name of functions so this route would be impossible using the method above. You may also want to access a controller that is not the default status controller, but not want to provide the controller name in the url. You can do this by adding values to the routes.php file. The following will create a route that matches the url provided in this paragraph for contact-us.
```php
$routes[] = new RouteAlias("contact-us", "home", "contactus", $CacheEnabled);
```

The constructor for the route is very simple. It takes 4 variables. $alias (the name of the slug you want to see in the url), $controller (the name of the controller you want to hit), $view (the name of the function within the controller), $usecache (whether you want this route to be cached using the frameworks cache engine. There are a few samples in the routes.php file for you to see and you can learn more by checking /classes/routes.class.php

## Responses
There is an object specificly added for handling the response. In most cases you will want to return a view that contains HTML to a user, but there will also be events where you want to return other things. I have an API in mind when building this, so returning raw XML may be something you want. You may also want to redirect rather than return a view like you would during a user login. Right now I have 2 response types added to the framework. Redirects and views. A redirect is very simple. In your controller method simply use the following.
```php
return Response::Redirect("some url");
```
Returning views are also quite simple, but there is a lot more flexibility. I will provide a section below that gives a more in depth description on how the views work.

## Views
There are 2 components to a view that need to be covered here. The first is how to actually return a view from the controller action. You can do this using the same method as a redirect. 

```php
return Response::View("path-to-iew-file");
```

This line will work perfectly, but you will often find that you want to pass some data into the view. All variables from the method will not be available in the view file. This is why you need to pass variables to the view. The .NET equivalent here would be using the ViewBag and with Laravel its quite similar. You can do this by calling the AddVar method of the class. You can add as many variables as you like. 
```php
return Response::View("path-to-iew-file")->AddVar("pagetitle", $pagetitle)->AddVar("listresults", $listresults);
```
### View Template
The second part of the view is the actual html section of the view with the view template. Each view will use a view template which I will go into more detail with below. Based on what you passed to the view, you can do what you like. The variable can be accessed as if it were declared at the top of the PHP file. There is no markdown language here so just raw HTML and PHP to get the job done. I may upgrade in the future, but part of keeping it lightweight was to keep it simple. I have modeled this roughly off the Laravel system. 

View templates are located in /views/templates. The default template is PublicTemplate.php. This needs to contain the main core of your web page without the content body. In other words, the header and the footer. So how do you get the dynamic content added? There is a helper method to do this called renderTemplateContent("varname"); So if you wanted to add a title tag to your HTML, this needs to be dynamic. Declaring the following will allow you to do so (i will explain how the value gets there after).
```php
<title><?php echo renderTemplateContent("pagetitle");?></title>
```
So the next part is finding a way to make sure that "pagetitle" actually has a value. This is done in the view file. Previously i mentioned that you can provide a view from the response object in the main controller action. In almost every case a site will have an index, so i will use this as an example. 
```php
<?php 
declareTemplateContent("pagetitle", "Main Index Page");

ob_start();
?>
<h1>Thank You</h1>
<p>This is the body of my home page</p>
<?php
$content = ob_get_clean();

declareTemplateContent("maincontent", $content);
```

## Database Communication
Since this is written in PHP, you can communicate with the database in whatever way you wish, but there is a helper library built into the framework that will allow you to make sql queries much easier. You can provide your database details in the config.php file using the following variables.
```php
define("DBHOST", "localhost");
define("DBUSERNAME", "root");
define("DBPASSWORD", '');
define("DBNAME", "dbserver");
```
You can then perform SQL queries using any of the methods in the /classes/database.class.php. Calling any method of this class will result in the data being logged so you can keep track of any database failures that occur without needing to print them on the screen of a production website. Here is a list of all the supported methods.

1. ExecuteQuery($sql, $fields = null, $autoID = false, $dbstring = null)
2. GetSQLResults($sql, $fields = null, $multiRow = true, $dbstring = null)
3. GetCount($sql, $fields, $dbstring = null)

## Page HTML Caching
To make the framework as efficient and quick as possible, I have built a caching framework directly into the core. In the config.php file, there are 2 constants that can be defined that control the cache. I would suggest that the cache is disabled during development, but once you are ready to go into production the following variables will enable the cache.
```php
define("ENABLECACHE", false);
define("CACHETTL", 600);
```
*Please note that your web server will need to have write access to the root of this directory in order to be able to write the html files to cache.
