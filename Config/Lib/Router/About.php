<?php
\Lib\Router::this()->respond('get', 'about', function () {
    include _HTML.'Static/about.html';
});
