# Laboratory-Activity-Developing-a-Forum-Web-App

# Setup
# Composer Install
# Run 'databse/schema.sql' in phpmyadmin inside the sql tab
# Create 'config/config.php'
# <?php
   return [
       'db_dsn'  => 'mysql:host=127.0.0.1;dbname=forum_lab;charset=utf8mb4',
       'db_user' => 'root',
       'db_pass' => '',
   ];
# php -S localhost:8000 -t public public/index.php
# Open http://localhost:8000/discussions/1

