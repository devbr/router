# Access Manager - Router

[![Latest Stable Version](https://poser.pugx.org/devbr/website/v/stable)](https://packagist.org/packages/devbr/router)
[![Latest Unstable Version](https://poser.pugx.org/devbr/website/v/unstable)](https://packagist.org/packages/devbr/router)
[![License](https://poser.pugx.org/devbr/website/license)](https://packagist.org/packages/devbr/router)
[![Total Downloads](https://poser.pugx.org/devbr/website/downloads)](https://packagist.org/packages/devbr/router)
[![Monthly Downloads](https://poser.pugx.org/devbr/website/d/monthly)](https://packagist.org/packages/devbr/router)

## Install

Use Composer for easy installation:

```php
Composer require devbr/router 
```

Or install the full base for PHP websites, at "https://github.com/devbr/website".

More info: https://packagist.org/packages/devbr/router

## Access Management

```TODO: translate to english``` 

Depois de instalado o arquivo de configuração (Config\Devbr\Router), é possível indicar as regras de resposta a solicitações de acesso ao site ou aplicação.

```php
namespace Config\Devbr;

class Router
{
    function __construct($router)
    {
        $router->respond('get', '/', 'Site\Front::page');
    }
}
```
Este é o arquivo básico que acompanha a instalação do Router, podendo ser encontrado em "/Config/Devbr/Router.php" (ou na pasta [vendor]/devbr/router/Config/Devbr/Router.php). É neste arquivo que fazemos a configuração de acesso de nossa aplicação ou site.

A função "respond", responsável por adicionar as rotas de resposta conforme a solicitação de acesso, tem a seguinte sintaxe:

```shell
TODO: review examples and didactics

$router->respond( <type>, <request>, <controller>, [<action>]);
        
    <type>:       A string with the following methods: "all", "get", "post", "delete", "put", "patch".
                  Or specify a specific group: "get|post|delete".
                          
    <request>:    String of the requested URI (without site domain).
                        Ex.: "about/me" ==> http://site.com/about/me
            
    <controller>: Class (object) to manage the request.
                  Name must be a complete string, with NAMESPACE + CLASSNAME. 
                        Ex.: "Devbr\User".
                  Alternatively you can use the following format: "controller::action". 
                        Ex.: "Devbr\User::login".
                  The Controller can also be an anonymous function that receives (or not)
                  parameters of the regular expression in <request>.
                        Ex.: $router->respond('get', 
                                              '/(*)/(*)/(*)', 
                                              function($rqst, $params){ 
                                                  exit( '<pre>'.print_r($params, true));
                                              }
                                             );
                    -- If you request "http://site.com/test/me/now", print on the screen "test me now".
            
    <action>:     Optional to indicate an action. 
                        Ex.: "login".
```

## Namespace

```TODO: translate to english```

O NAMESPACE tem seu "root" na pasta do PHP em seu site ou aplicação ("/.php").

Se você instalou o "https://github.com/devbr/website" já terá esta configuração, caso não, acrescente isto em seu composer.json:

```shell
"autoload": {
        "psr-4": {"": ".php/"}
    }
```
<b><< a pasta pode ter outro nome, conforme sua escolha >></b>

Em um servidor Linux, rodando Apache, o root pode estar no seguinte caminho:
```php
/var/www/site/.php/

--- pode variar conforme a configuração do servidor.
```
A partir dessa pasta você pode chamar qualquer recurso (classe), usando o caminho relativo, o patch (caminho) do arquivo da classe.

Vamos considerar (para exemplo) que a sua classe está no seguinte caminho:

```php
/var/www/site/.php/Site/Front/Page.php
```

Para montar esse objeto use:

```php
$page = new Site\Front\Page;
```

Ou você pode usar a declaração "use", para ficar mais elegante:

```php
//Logo abaixo do "namespace":
use Site\Front\Page;
 ....

//Dentro de um método da classe...
$page = new Page;
```

