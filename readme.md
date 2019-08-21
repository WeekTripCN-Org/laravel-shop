# MYSQL 数据相关

## 导出表数据
> mysqldump -uroot -t laravel-shop admin_menu admin_permissions admin_role_menu admin_role_permissions admin_role_users admin_roles admin_ user_permissions admin_users > database/admin.sql
> 
## 清空数据
> php artisan migrate:fresh 

## 导入后台数据
> mysql -uroot laravel-shop < database\admin.sql

## 生成假数据
> php artisan db:seed

# 队列相关
## 执行队列
> php artisan queue:work

# 脚本
## 备份管理后台数据
> 加执行权限： chmod +x back_admin_db.sh

## 内网穿透运行方式
> ngrok http -host-header=shop.test -region eu 80