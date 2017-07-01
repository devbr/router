<?php
\Devbr\Router::this()->respond('get', 'about', function () {
    include \Config\App::Html().'Static/about.html';
});
