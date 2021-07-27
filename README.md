PHP Extreme Micro Router


```
Route::get('/my_page', function() {
    echo 'route /my_page';
});
  
  
if (!Route::run()) {
    echo 'PAGE NOT FOUND!';
}
```
