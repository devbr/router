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

Para facilitar o acesso as configurações, sugiro mover a pasta "Config" para o "root" de sua aplicação PHP e acrescentar o seguinte em seu arquivo **composer.json**:

```json
...
    "autoload": {
        "Config\\": {"": ".php/Config/"}
    }
...
```
<b><< supondo que ".php/" seja seu fallback (ou raiz) para os arquivos PHP >></b>

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

O NAMESPACE tem seu "root" (fallback) na pasta do PHP em seu site ou aplicação.

Se você instalou o "https://github.com/devbr/website" já terá esta configuração, caso não, acrescente isto em seu composer.json:

```json
...
    "autoload": {
        "psr-4": {"": ".php/"}
     }
...    
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

## Usando o ROUTER

No front controller da sua aplicação web (geralmente o arquivo index.php), você pode ter o seguinte:

```php
<?php

//Carregando o autoloader do Composer
include '[vendor]/autoload.php';

//Montando e rodando o Router
(new Devbr\Router)->run();
```

A pasta "vendor" do Composer pode estar, por exemplo, na sua pasta raiz dos arquivos PHP (ex.: ".php/").
O Router, baseado nas configuração (Config\Devbr\Router), vai identificar as requisições e chamar o Controller (e action), passando os parâmetros, seguindo a configuração que você definiu para a rota.

Você pode querer que o Router apenas identifique as requisições, retornando os dados para que você use algum "midware", antes de chamar o controlador. Para isso, basta desativar o "autorun" do Router, na montagem do objeto:

```php
<?php

//Carregando o autoloader do Composer
include '[vendor]/autoload.php';

//Montando e rodando o Router
$router = (new Devbr\Router(false))->run();

//For example, only --¬
echo '<b>Controller:</b> '.$router->getController();

//Or ...
echo '<pre>'.print_r($router, true).'</pre>';
```

## Modo CLI (command line)

O Router também pode ser configurado para tratar acesso em linha de comando (terminal). Para esse caso, o method "CLI" é automáticamente detectado.

O namespace default é "Devbr\Cli" e pode (deve) ser configurado conforme as necessidades de sua aplicação. Este namespace está ai para funcionar em conjunto com o componente https://github.com/devbr/tools e obter algumas funcionalidades de apoio ao desenvolvedor.

Com o Devbr\Tools instalado, você pode digitar no terminal:

```shell
php index.php Main
```

Onde "Main" é a classe principal desse pacote e, no caso acima, irá obter um "help" das funções disponíveis. Ou ``` Controller not found! ``` caso o Devbr\Tools não esteja instalado.

Para criar seus próprios objetos para acesso via linha de commando, precisará indicar ao Router o *namespace* de suas classes para o CLI, na configuração do Router:

```php
namespace Config\Devbr;

class Router
{
    function __construct($router)
    {
        $router->setNamespaceCliPrefix('Cli');
        $router-> ... //demais configurações
```

Nesse exemplo acima, "Cli" é o diretório raíz para as suas classes (a partir do fallback). 
Considere o seguinte caminho para a sua classe "Tests" *(estou confiando que ".php" é o fallback principal, previamente configurado no **composer.json**, conforme visto anteriormente)*:

```shell
/var/www/.php/Cli/Tests.php
```

Abrindo um terminal e digitando ``` php index.php Tests::action par1 par2 ```, o Router vai montar a classe **Cli\Tests**, chamar o método **action**, passando **par1 e par2** como parâmetros.

Caso você digite ``` php index.php Tests ``` (sem o action) o Router chamará o método **cliHelp** (default), se existir. Você deve criar esse método em suas classes CLI para exibir algum texto de ajuda, para o usuário.
