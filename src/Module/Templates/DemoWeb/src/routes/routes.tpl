<?php

return function ($route) {
    $route->get('[:alpha:2]?', 'PageController', 'home')->name('home');
    $route->get('[:alpha:2]?/about', 'PageController', 'about')->name('about');

    $route->get('[:alpha:2]?/posts', 'PostController', 'posts')->name('posts');
    $route->get('[:alpha:2]?/post/[id=:any]', 'PostController', 'post');

    $route->group('guest', function ($route) {
        $route->add('[:alpha:2]?/signin', 'GET|POST', 'AuthController', 'signin')->name('signin');
        $route->add('[:alpha:2]?/signup', 'GET|POST', 'AuthController', 'signup')->middlewares(['Signup'])->name('signup');
        $route->get('[:alpha:2]?/activate/[token=:any]', 'AuthController', 'activate')->middlewares(['Activate']);
        $route->add('[:alpha:2]?/forget', 'GET|POST', 'AuthController', 'forget')->middlewares(['Forget']);
        $route->add('[:alpha:2]?/reset/[token=:any]', 'GET|POST', 'AuthController', 'reset')->middlewares(['Reset']);
        $route->get('[:alpha:2]?/resend/[code=:any]', 'AuthController', 'resend')->middlewares(['Resend']);
        $route->add('[:alpha:2]?/verify/[code=:any]?', 'GET|POST', 'AuthController', 'verify')->middlewares(['Verify']);
    })->middlewares(['Guest']);

    $route->group('auth', function ($route) {
        $route->get('[:alpha:2]?/signout', 'AuthController', 'signout');
        $route->get('[:alpha:2]?/my-posts', 'PostController', 'myPosts')->middlewares(['Editor']);
        $route->get('[:alpha:2]?/my-posts/create', 'PostController', 'createFrom')->middlewares(['Editor']);
        $route->post('[:alpha:2]?/my-posts/create', 'PostController', 'create')->middlewares(['Editor']);
        $route->get('[:alpha:2]?/my-posts/amend/[id=:any]', 'PostController', 'amendForm')->middlewares(['Editor', 'Owner']);
        $route->post('[:alpha:2]?/my-posts/amend/[id=:any]', 'PostController', 'amend')->middlewares(['Editor', 'Owner']);
        $route->get('[:alpha:2]?/my-posts/delete/[id=:any]', 'PostController', 'delete')->middlewares(['Editor', 'Owner']);
        $route->get('[:alpha:2]?/my-posts/delete-image/[id=:any]', 'PostController', 'deleteImage')->middlewares(['Editor', 'Owner']);
        $route->get('[:alpha:2]?/account-settings', 'AccountController', 'form');
        $route->post('[:alpha:2]?/account-settings/update', 'AccountController', 'update')->middlewares(['Update']);
        $route->post('[:alpha:2]?/account-settings/update-password', 'AccountController', 'updatePassword')->middlewares(['Password']);
    })->middlewares(['Auth']);
};