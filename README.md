## Simple Slim

#### Características
- Slim Framework 3 (https://www.slimframework.com/docs/)
- Twig View (https://github.com/slimphp/Twig-View) (https://twig.symfony.com/doc/2.x/)
- Protection CSRF (https://github.com/slimphp/Slim-Csrf)


### Instalacion

#### 1 Manual Install
You can manually install SlimApp by cloning this repo or download the zip file from this repo, and run ```composer install```.
```
$git clone https://github.com/guardeivid/SlimApp.git .
$composer install
```

## Directory Structure

### The Root Directory
- The `app` Directory
- The `bootstrap` Directory
- The `public` Directory
- The `storage` Directory
- The `vendor` Directory

### The App Directory
- The `Controllers` Directory
- The `Middelware` Directory
- The `Models` Directory
- The `Validation` Directory
    - The `Exceptions` Directory
    - The `Rules` Directory    
- The `Views` Directory

- The `routes.php` File


## Routing
Routing configuration is located in ```app/routes.php```, 

Route to closure
```php
$app->get(...)

$app->get('/', function() use ($app){
    $app->view->display('welcome.twig');
});
```

Route to controller method
```php
/** get method */
$app->get('/', 'SomeController:someMethod');

/** post method */
$app->post('/post', 'PostController:create');

/** put method */
$app->put('/post/:id', 'PostController:update');

/** delete method */
$app->delete('/post/:id', 'PostController:destroy');
```

Route Name
```php
/** route name */
$app->get('/admin', 'AdminController:index')->setName('admin');
```

Route Middleware
```php
/** route middleware */
$app->get('/admin', 'AdminController:index')->add('SomeMiddleware');
```

Route group
```php
/** Route group to book resource */
$app->group('/book', function(){
    $this->get('/', 'BookController:index'); // GET /book
    $this->post('/', 'BookController:store'); // POST /book
    $this->get('/create', 'BookController:create'); // Create form of /book
    $this->get('/:id', 'BookController:show'); // GET /book/:id
    $this->get('/:id/edit', 'BookController:edit'); // GET /book/:id/edit
    $this->put('/:id', 'BookController:update'); // PUT /book/:id
    $this->delete('/:id', 'BookController:destroy'); // DELETE /book/:id
});
```

## Controller
Controllers are located in ```app/Controllers``` directory, you may extends the *Controller* to get access to predefined helper.
You can also place your controller in namespace to group your controller.

file : app/controllers/HomeController.php
```php
namespace App\Controllers;

use App\Controllers\Controller;

class HomeController extends Controller{

    public function welcome(){
        $this->data['title'] = 'Some title';

        return $this->view->fetch('home.twig', $this->data);
        // o
        return $this->view->render($this->response, 'home.twig', $this->data);
    }
}
```

Controller class have access to differents properties:
- $container
- $request
- $response
- $router
- $db
- $view
- $validator
- $data 

## View
Views file are located in ```app/Views``` directory in twig format, there is home.twig with 'body' block as default master template
shipped with SlimApp that will provide default access to published js variable.

For detailed Twig documentation, please refer to http://twig.sensiolabs.org/documentation


file : app/Views/welcome.twig
```html
{% extends 'home.twig' %}
{% block body %}
    Welcome to SlimApp
{% endblock %}

```

### Rendering view inside controller
If your controller extends the Controller class, you will have access to $data property which will be the placeholder for all
view's data and $view property equal a Facade View::.

```php
$this->view->fetch('welcome.twig', $this->data);

$this->view->render($this->response, 'home.twig', $this->data);
```

## Validation
If your controller extends the Controller class, you will have access to $validator property which will be the placeholder for all
validations from request. 
[https://respect-validation.readthedocs.io/](https://respect-validation.readthedocs.io/)

```php
$validation = $this->validator->validate($this->request, [
    'name' => v::noWhitespace()->notEmpty()
]);

if ($validation->failed()){
    return $this->response->withRedirect($this->router->pathFor('home'));
}
```

And from the view you can access to `{{ error.property }}`
```html
<div class="form-group{{ errors.email ? ' has-error' : '' }}">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" class="form-control">
    {% if error.email %}
    <span class="help-block">{{ error.email | first }}</span>
    {% endif %}
</div>
```

Also you can acces to old input in the view `{{ old.property }}`
```html
<div class="form-group{{ errors.email ? ' has-error' : '' }}">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" class="form-control" value="{{ old.email }}">
    {% if error.email %}
    <span class="help-block">{{ error.email | first }}</span>
    {% endif %}
</div>
```

## CRSF

There are two way the include crsf in the view

1. Extension Twig
```html
{{ crsf_field() | raw }}
```

2. Middleware
```html
{{ crsf.field | raw }}
```

Register
```php
//If Middleware
$app->add(new \App\Middelware\CsrfViewMiddelware($container));

//And... check 

//Global, for all routes (default)
$app->add($container->csrf);

//or determinate route, (comment global)
$app->get('/', 'Controller:index')->add($container->csrf);
```

## Classes

| Classes  |
|---|
| Slim\App |
| Slim\Http\Request |
| Slim\Http\Response | 
| Slim\Views\Twig |


```php
use \Slim\App;
$app->add($callable); //addMidellware
$app->redirect($from, $to, $status = 302);
$app->run();

$app->get($pattern, $callable);
$app->post($pattern, $callable);
$app->put($pattern, $callable);
$app->delete($pattern, $callable);
$app->any($pattern, $callable);
$app->options($pattern, $callable);
$app->patch($pattern, $callable);
$app->group($pattern, $callable);

use Slim\Http\Request;
$request->getMethod();
$request->withMethod();
$request->isMethod($method);
$request->isGet();
$request->isPost();
$request->isPut();
$request->isPatch();
$request->isDelete();
$request->isHead();
$request->isOptions();
$request->isXhr();
$request->getRequestTarget();
$request->withRequestTarget($requestTarget);
$request->getUri();
$request->withUri(UriInterface $uri, $preserveHost = false);
$request->getContentType();
$request->getMediaType();
$request->getMediaTypeParams();
$request->getContentCharset();
$request->getContentLength();
$request->getCookieParams();
$request->getCookieParam($key, $default = null);
$request->withCookieParams(array $cookies);
$request->getQueryParams();
$request->withQueryParams(array $query);
$request->getUploadedFiles();
$request->withUploadedFiles(array $uploadedFiles);
$request->getServerParams();
$request->getServerParam($key, $default = null);
$request->getAttributes();
$request->getAttribute($name, $default = null);
$request->withAttribute($name, $value);
$request->withAttributes(array $attributes);
$request->withoutAttribute($name);
$request->getParsedBody();
$request->withParsedBody($data);
$request->getParam($key, $default = null);
$request->getParsedBodyParam($key, $default = null);
$request->getQueryParam($key, $default = null);
$request->getParams(array $only = null);

use \Slim\Http\Response;
$response->getStatusCode();
$response->withStatus($code, $reasonPhrase = '');
$response->withHeader($name, $value);
$response->write($data);
$response->withRedirect($url, $status = null);
$response->withJson($data, $status = null, $encodingOptions = 0);
$response->isEmpty();
$response->isOk();
$response->isSuccessful();
$response->isRedirect();
$response->isRedirection();
$response->isForbidden();
$response->isNotFound();
$response->isClientError();
$response->isServerError();

use \Slim\Views\Twig;
$view->fetch($template, $data = []);
$view->render(ResponseInterface $response, $template, $data = []);

```

Run 
```sh
 composer dump-autoload -o
```