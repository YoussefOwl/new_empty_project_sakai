<!-- ----------------- Le /etc/nginx/sites-enabled/default ----------------- -->
server {
    listen 80;
    server_name api-depenses.promagasin.ma;
    return 301 https://$host$request_uri;
}
server {
    listen 80;
    server_name depenses.promagasin.ma;
    return 301 https://$host$request_uri;
}
<!-- ---------------------- Le /etc/nginx/nginx.conf ----------------------- -->
### Le back-Office depenses
server {
    listen 443 ssl;
    server_name depenses.promagasin.ma; # Replace with your domain
    root /var/www/crm_depenses/bo;
    index index.html index.htm;
    location / { try_files $uri $uri/ /index.html; }
    ssl_certificate /etc/letsencrypt/live/depenses.promagasin.ma/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/depenses.promagasin.ma/privkey.pem; # managed by Certbot
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers HIGH:!aNULL:!MD5; 
}
### l'api de depenses
server {
    listen 443 ssl;
    server_name api-depenses.promagasin.ma;
    root /var/www/crm_depenses/api/public;
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    index index.php;
    charset utf-8;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;
    location ~ \.php$ {
        include fastcgi_params;
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
        fastcgi_connect_timeout 300s;
    }
    location ~ /\.(?!well-known).* {
        deny all;
    }
    ssl_certificate /etc/letsencrypt/live/api-depenses.promagasin.ma/fullchain.pem;  # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/api-depenses.promagasin.ma/privkey.pem;  # managed by Certbot
}

<!-- --------------------------- le nginx .conf ---------------------------- -->

client_max_body_size 25M;
proxy_connect_timeout       600;
proxy_send_timeout          600;
proxy_read_timeout          600;
send_timeout                600;
sendfile on;
tcp_nopush on;
types_hash_max_size 2048;

[mysqld]
# InnoDB settings
innodb_buffer_pool_size = 24G
innodb_log_file_size = 1G
innodb_flush_log_at_trx_commit = 2
innodb_io_capacity = 2000
innodb_thread_concurrency = 0
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
innodb_buffer_pool_instances = 8
innodb_log_buffer_size = 64M

# General settings
key_buffer_size = 256M
max_allowed_packet = 256M
thread_stack = 256K
thread_cache_size = 64

# Connection settings
max_connections = 500
table_open_cache = 2000
query_cache_size = 0
sql_mode = NO_ENGINE_SUBSTITUTION

<!-- ------------------ Tableau récapitulatif fpm/php.ini ------------------ -->
<!-- -------- Fichier de configuration	Paramètre	Valeur recommandée -------- -->
php.ini	memory_limit	512M
max_execution_time	60
max_input_time	60
opcache.enable	1
opcache.memory_consumption	256
opcache.max_accelerated_files	10000
opcache.validate_timestamps	0

<!-- -------------------- www.conf (PHP-FPM)	pm	dynamic -------------------- -->
pm.max_children	150
pm.start_servers	20
pm.min_spare_servers	10
pm.max_spare_servers	30
pm.max_requests	500
nginx.conf	worker_processes	auto
worker_connections	4096
keepalive_timeout	30
client_max_body_size	50M