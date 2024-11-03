# slim系のnodeから構築することを定義
FROM node:22.0.0-bullseye
# コンテナ内の作業ディレクトリを作成
WORKDIR /app
# パッケージ関連ファイルを先にコピーして依存関係をインストール
COPY package*.json ./

RUN apt update && npm install

# アプリケーション全体をコピー
COPY . .

# Viteで使用するポートを公開
EXPOSE 3000

# デフォルトのコマンド（コンテナ起動時に実行する）
CMD ["npm", "run", "dev", "--", "--host"]