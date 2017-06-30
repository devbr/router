<?php
\Devbr\Router::this()->respond('get', 'about', function () {
    echo '<h1>About</h1><p>'.__FILE__.'</p>';
});
