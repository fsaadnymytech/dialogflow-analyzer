<?php
//Rutas de vistas:
Route::get("/", function (){ 
    return ((new View())->getRenderedView("index.php"));
});

Route::get("docs_get_zip", function (){ 
    return ((new View())->getRenderedView("docs_get_zip.php"));
});

Route::post("generate_file", function (){ 
    return ((new FilesGenerator())->generateFile());
});

Route::get("img/docs_img_1.png", function (){ 
    header("Content-type: image/png");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/img/docs_img_1.png");
});

Route::get("img/docs_img_2.png", function (){ 
    header("Content-type: image/png");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/img/docs_img_2.png");
});

Route::get("img/docs_img_3.png", function (){ 
    header("Content-type: image/png");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/img/docs_img_3.png");
});

Route::get("img/docs_img_4.png", function (){ 
    header("Content-type: image/png");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/img/docs_img_4.png");
});

//Static resources routes:
Route::get("img/favicon.ico", function (){ 
    header("Content-type: image/x-icon");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/img/favicon.ico");
});

Route::get("css/index.css", function (){ 
    header("Content-type: text/css");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/css/index.css");
});

Route::get("css/docs.css", function (){ 
    header("Content-type: text/css");
    return 
        (new StaticResource())->getStaticResource("app/views/assets/css/docs.css");
});
