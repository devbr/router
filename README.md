# Access Manager - Router

## Install

Use Composer for easy installation:

```php
Composer require devbr/router 
```

Or install the full base for PHP websites, at "https://github.com/devbr/website".

More info: https://packagist.org/packages/devbr/router

## Access Management

Depois de instalado o arquivo de configuração (Config\Router), é possível indicar as regras de resposta a solicitações de acesso ao site ou aplicação.

```php
namespace Config;

class Router 
{
    function routers(&$router)
    {
        $router->respond('get', '/', 'Site\Front::page');
    }
}
```
Este é o arquivo básico que acompanha a instalação do Router, podendo ser encontrado em ".php/Config/Router". É neste arquivo que fazemos a configuração de acesso de nossa aplicação ou site.

A função "respond", responsável por adicionar as rotas de resposta conforme a solicitação de acesso, tem a seguinte sintaxe:

```shell
$router->respond( <type>, <request>, <controller>, [<action>]);
        
    <type>:       A string with the following methods: "all", "get", "post", "delete", "put", "patch".
                  Or specify a specific group: "get|post|delete".
                          
    <request>:    String of the requested URI (without site domain).
                        Ex.: "about/me" ==> http://site.com/about/me
            
    <controller>: Class (object) to manage the request.
                  Name must be a complete string, with NAMESPACE + CLASSNAME. 
                        Ex.: "Lib\User".
                  Alternatively you can use the following format: "controller::action". 
                        Ex.: "Lib\User::login".
                  The Controller can also be an anonymous function that receives (or not)
                  parameters of the regular expression in <request>.
                        Ex.: $router->respond('get', 
                                              '/(*)/(*)/(*)', 
                                              function($one, $two, $three){ 
                                                  exit( $one.' '.$two.' '.$three);
                                              }
                                             );
                    -- If you request "http://site.com/test/me/now", print on the screen "test me now".
            
    <action>:     Optional form to indicate an action. 
                        Ex.: "login".
```

## Namespace

O NAMESPACE tem seu "root" na pasta do PHP em seu site/app (".php").
Por exemplo, em um servidor Linux com Apache:

```php
/var/www/site/.php/
```
A partir dessa pasta você pode chamar qualquer recurso (classe), usando o caminho relativo, o patch (caminho) do arquivo da classe.

Vamos considerar (para exemplo) que a sua classe está no seguinte caminho:

```php
/var/www/site/.php/Site/Front/Page.php
```

Para montar esse objeto use:

```php
$page = new Site\Front\Page();
```

Ou você pode usar a declaração "use", para ficar mais elegante:

```php
//Logo abaixo do "namespace":
use Site\Front\Page;
 ....

//Dentro de uma classe/função...
$page = new Page;
```

