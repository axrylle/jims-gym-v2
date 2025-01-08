# jims-gym-v2
 
composer install
cp .env.example .env
//configure .env file 
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=jimsGym
    DB_USERNAME=root
    DB_PASSWORD=

php artisan key:generate
php artisan storage:link

php artisan migrate:fresh --seed
php artisan db:seed --class=membershipSeeder
php artisan db:seed --class=MembershipUserSeeder

