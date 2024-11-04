# PHP 8.2とComposerを含む公式イメージを使用
FROM php:8.2-fpm

# ComposerとLaravelに必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Composerをインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# ホストのLaravelプロジェクトをコピー
COPY . /var/www/html
# COPY . /var/www/html

# 必要なディレクトリを作成し、権限を設定
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

# 環境変数を設定
ENV COMPOSER_ALLOW_SUPERUSER=1

# Laravelの依存関係をインストール
RUN composer install --no-scripts --no-autoloader

RUN composer dump-autoload

# サーバーのポートを公開
EXPOSE 8000

# Laravelの開発サーバーを起動
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
