## 一、下载项目代码
 - git clone https://github.com/WeekTripCN-Org/laravel-shop.git

## 二、下载 Composer 依赖
 - composer install

## 三、加载 NodeJs 依赖
 - SASS_BINARY_SITE=http://npm.taobao.org/mirrors/node-sass yarn
  
## 四、创建 .env 文件
 - cp .env.example .env
 - php artisan key:generate
 - 修改 .env配置
  
## 五、执行数据库迁移
 - php artisan migrate
  
## 六、生成假数据
 - php artisan db:seed
 - php artisan db:seed --class=DDRProductsSeeder
  
## 七、导入后台数据
 - mysql -uroot -p laravel-shop < database/admin.sql

## 八、后台默认账号密码
 - admin@admin

## 九、构建前端代码
 - npm install
 - yarn production
  