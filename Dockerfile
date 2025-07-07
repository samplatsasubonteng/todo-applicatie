FROM php:8.2-apache

# Installeer de vereiste extensies
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Kopieer alle projectbestanden
COPY . /var/www/html/

EXPOSE 80

# ik heb het via render gedaan omdat
# vercel niet wilt werken bij mij ik moet hun een mail sturen maar ze antwoorden pas binnen 2 werkdagen