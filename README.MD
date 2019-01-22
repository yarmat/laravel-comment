# Laravel comments with Vue.js
Package for using comments with Vue.js component

## Installing
<pre>composer require yarmat/laravel-comment</pre>

You must publish Vue Component, Languages, migration and config;

<pre>php artisan vendor:publish --provider="Yarmat\Comment\CommentServiceProvider"
</pre>

After publishing you can create comments-tables by running the migrations:

<pre>php artisan migrate</pre>

Add middleware
```php
    protected $routeMiddleware = [
        ...
        'comment' => 'Yarmat\Comment\Http\Middleware\CommentMiddleware'
    ];
```

## Settings

You should edit config file

<pre>config/comment.php</pre>
##### Limit
Limit of comments when will you send a request for the route get (route('comment.get'))
```php
'limit' => '5',
```

##### Order

```php
'default_order' => 'DESC', // OR ASC. DESC - Newest, ASC - Oldest
```

##### Models
Change the User Model
```php
'models' => [
     'user' => 'Your User Model Path'
 ]        
```

##### Prefix
Change prefix, if you need to change route prefix
<pre>
{prefix}.store
{prefix}.destroy
{prefix}.update
{prefix}.count
{prefix}.get
</pre>

##### Middleware
You can assign a middleware to each route:
```php
    'middleware' => [
    
       'store' => ['throttle:15'],

        'destroy' => ['auth'],

        'get' => [],

        'update' => [],

        'count' => []
    ],
```

##### Models that will implement comments
```php
    'models_with_comments' => [
        'Blog' => App\Models\Blog::class,
    ],
```

##### Relations
Add relations for you comments 

```php
'comment_relations' => ['user'],
```

or

```php
comment_relations' => ['user' => function($query) {
    $query->select(['id', 'name', 'email']);
}, 'likes' => function($query){
    $query-> ...
}],
```

##### Validation
```php
        'auth' => [ // For Auth Users
            'message' => ['required', 'string', new \Yarmat\Comment\Rules\Spam(), new \Yarmat\Comment\Rules\AllowableSite()]
        ],
        'not_auth' => [ // For Not Auth Users
            'name' => 'required|alpha',
            'email' => 'required|email',
            'message' => ['required', 'string', new \Yarmat\Comment\Rules\Spam(), new \Yarmat\Comment\Rules\AllowableSite()]
        ],
        'messages' => []
```

##### TransformComment
Function that transform Comments Model before you get it in the Vue component
```php
    'transformFunction' => function ($item) {
        return [
            'id' => $item->id,
            'message' => $item->message,
            'isVisibleForm' => false,
            'date' => \Date::parse($item->created_at)->diffForHumans(),
            'user' => [
                'name' => $item->user->name ?? $item->name,
                'email' => $item->user->email ?? $item->email
            ],
            'children' => []
        ];
    },
```

##### Allowable Tags
Php function strip_tags() that cuts out all tags except those that you list in the string
```php
'allowable_tags' => '',
```

##### Spam Words
You can list spam words. Comments with these words will not be published.
```php
'spam_list' => ['spam'],
```

##### Spam Sites
You can list allowable sites. The comment will not be published if there is an unresolved link in it.
```php
'allowable_sites' => ['site.com'],
```

## Usage
F.e. you have model Post and you want to attach comments to it

##### Step 1
Add model to config 
```php
    'models_with_comments' => [
         'Post' => App\Post::class,
    ],
```

##### Step 2
Add <b>Yarmat\Comment\Traits\HasCommentTrait</b> and <b>Yarmat\Comment\Contracts\CommentContract</b> to your model Post:

```php
<?php 

use Illuminate\Database\Eloquent\Model;
use Yarmat\Comment\Contracts\CommentContract;
use Yarmat\Comment\Traits\HasCommentTrait;

class Post extends Model implements CommentContract
{
    use HasCommentTrait;
}    
```

##### Step 3

Add <b>Yarmat\Comment\Traits\CommenterTrait</b> to your User model

```php
<?php 

use Illuminate\Foundation\Auth\User as Authenticatable;
use Yarmat\Comment\Traits\CommenterTrait;

class User extends Authenticatable
{
    use CommenterTrait;
}    
```

##### Step 4
Include comment component to your Vue App
```js
Vue.component('comment-component', require('./components/comment/CommentComponent').default);
```

##### Step 5

Include to your View
 
```html
<comment-component></comment-component>
```

##### Step 6

Include config to your View

```php
<?php
    $post = Post::first();
```
```html
 {!! \Comment::config('Post', $post->id) !!}
```

##### Finish
That is all! The you can customise vue component to your template. Component is here:
<pre>
resources/js/components/comment
</pre>

## License

The Laravel-comment is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).




