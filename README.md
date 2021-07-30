PHP Extreme Micro Router


```
Route::get('/my_page', function() {
    echo 'route /my_page';
});
  
Route::regex('/^\/page\/(\d+)\/$/', function($num) {
    echo "page number: $num";
});
  
// START DISPATCH
if (!Route::run()) {
    echo 'PAGE NOT FOUND!';
}
```
