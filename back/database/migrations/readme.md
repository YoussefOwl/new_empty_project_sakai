# Migrations
// timestamps c'est les champs created_at et update_at
php artisan make:model nom_table -m // cette commande crée un modèle et une Migrations
php artisan migrate // pour ajouter la table sur la base de données
php artisan migrate --database=database_name
php artisan migrate --path="/database/migrations/dir"
# ---------------------------------------------------------------------------- #
#                                BACKUP DATABASE                               #
# ---------------------------------------------------------------------------- #
mysqldump -u root -p my_db > /var/www/path/file_name.sql --skip-tz-utc
# ---------------------------------------------------------------------------- #
#                                 LES COMMANDES                                #
# ---------------------------------------------------------------------------- #