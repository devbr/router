<?php

$router->respond('get', 'about', function () {
    $htmlpage = __DIR__.'/Html/Static/about.html';

    if (file_exists($htmlpage)) {
        include $htmlpage;
    } else {
        exit('<div style="position:fixed;top:50%;left:50%;margin:-50px 0 0 -100px;text-align:center;width:200px">Page "About" not found!<br/><a href="./">Home Page</a></div>');
    }
});
