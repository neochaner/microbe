PHP Extreme Micro Router


```
Route::get('/my_page', function() {
    echo 'route /my_page';
});
  
Route::get('/page/{num}/', function($num) {
    echo "number: $num";
});
  
// START DISPATCH
if (!Route::run()) {
    echo 'PAGE NOT FOUND!';
}
```
