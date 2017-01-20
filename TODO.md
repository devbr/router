# TODO
Sugestões de futuras implementações, modificações etc.

## Sugestões para a edição "Orange Summer III"
Publicação prevista para <b>31/01/2017 às 12:00hm</b> - código da versão: ```20170131120000```.

----

### 1 - Inserir função no ```Config/Router``` para configurar a resposta em situação de <b>erro</b>.

Sugestão de sintaxe:

```php
namespace Config;

class Router 
{
    function routers(&$router)
    {
        //Página inicial
        $router->respond('get', '/', 'Site\Front::page');
         ...
        
        //Errors
        $router->error('404', 'get', 'Site\Error::respondGet404')
               ->error('404', 'post|send|delete', 'Site\Error::respondPost404')
               ->error('414', 'get', 'Site\Error::respondGet414');
        ...    
    }
}
```

### 2 - Registro de uma 'alias' (apelido) opcional para os rotas.

Sugestão de sintaxe:

```php
namespace Config;

class Router 
{
    function routers(&$router)
    {
        //Rota da página inicial
        $router->alias('home')
               ->respond('get', '/', 'Site\Front::page');
               
        //About com acesso normal (página html) ou por uma API via post:       
        $router->alias('about')
               ->respond('get', 'about', 'Site\About::page')
               ->respond('post', 'about', 'Site\About::post');
         ...
    }
}
```

O uso pode ser em qualquer lugar onde se queira mostrar o caminho (url):

```php
Vá para a <a href="<?php Lib\Router::uri('home')?>">página inicial</a> do site e...

Obtenha mais informações sobre nossa empresa nesta <a href="<?php Lib\Router::uri('about', 'get')?>">página</a>.
```

Para obter a rota quando é indicado mais de um método, como em ``` ->respond('post|delete|send', ... ```, basta indicar um dos métodos configurados: ``` Lib\Router::uri('delete') ```.


