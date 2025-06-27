const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .vue()
   .extract(['laravel-echo', 'pusher-js']);