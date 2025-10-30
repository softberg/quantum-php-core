<?php

return function ($route) {
    $route->get('[:alpha:2]?/posts', 'PostController', 'posts')->name('posts');
    $route->get('[:alpha:2]?/post/[uuid=:any]', 'PostController', 'post');

    $route->post('[:alpha:2]?/signin', 'AuthController', 'signin');
    $route->post('[:alpha:2]?/signup', 'AuthController', 'signup')->middlewares(['Signup']);
    $route->post('[:alpha:2]?/forget', 'AuthController', 'forget')->middlewares(['Forget']);
    $route->get('[:alpha:2]?/activate/[token=:any]', 'AuthController', 'activate')->middlewares(['Activate']);
    $route->post('[:alpha:2]?/reset/[token=:any]', 'AuthController', 'reset')->middlewares(['Reset']);
    $route->get('[:alpha:2]?/resend/[code=:any]', 'AuthController', 'resend')->middlewares(['Resend']);
    $route->post('[:alpha:2]?/verify', 'AuthController', 'verify')->middlewares(['Verify']);

    $route->group('auth', function ($route) {
        $route->get('[:alpha:2]?/my-posts', 'PostManagementController', 'myPosts')->middlewares(['Editor']);
        $route->post('[:alpha:2]?/my-posts/create', 'PostManagementController', 'create')->middlewares(['Editor']);
        $route->put('[:alpha:2]?/my-posts/amend/[uuid=:any]', 'PostManagementController', 'amend')->middlewares(['Editor', 'Owner']);
        $route->delete('[:alpha:2]?/my-posts/delete/[uuid=:any]', 'PostManagementController', 'delete')->middlewares(['Editor', 'Owner']);
        $route->delete('[:alpha:2]?/my-posts/delete-image/[uuid=:any]', 'PostManagementController', 'deleteImage')->middlewares(['Editor', 'Owner']);

        $route->post('[:alpha:2]?/comments/create/[uuid=:any]', 'CommentController', 'create')->middlewares(['Comment']);

        $route->put('[:alpha:2]?/account-settings/update', 'AccountController', 'update')->middlewares(['Update']);
        $route->put('[:alpha:2]?/account-settings/update-password', 'AccountController', 'updatePassword')->middlewares(['Password']);
        $route->get('[:alpha:2]?/signout', 'AuthController', 'signout')->middlewares(['Signout']);
        $route->get('[:alpha:2]?/me', 'AuthController', 'me');
    })->middlewares(['Auth']);
};