# qscmf-curd-generator
增删查改自动生成器

## 安装

```php
composer require quansitech/qscmf-curd-generator
```

## 用法
1. 先创建数据库迁移文件
2. 在迁移代码中设置表、字段注释，生成器根据注释自动生成对应的代码

##### 注释格式
> 注释格式统一采用 @key=value; 的形式（最后的分号是格式的一部分）
>
> 如：设置字段title和type两个键值
>
> @title=发布时间;@type=date;

##### 注释类型

1. title
> 可用在表注释和字段注释中，表注释为必填，没有设置会报错
>
> ```php
>  //字段注释
>  $table->string('title', 20)->comment('@title=合作伙伴;');
> 
>  //表注释(laravel migration 没有提供表的注释封装)
>  \Illuminate\Support\Facades\DB::unprepared("ALTER TABLE `qs_partner` COMMENT = '@title=合作伙伴;'");
> ```

2. type

+ model 
> 表示该字段为外键，与另外一张表关联
> 
> 必填配置值
>
> table 关联的表名
> 
> show 关联表的描述字段
>
> 样例代码
>```php
> $table->mediumInteger('cate_id')->comment('@title=伙伴分类;@type=model;@table=qs_partner_cate;@show=title;');
>```

+ date 
> 日期类型 
> 样例代码
>```php
> $table->decimal('publish_date', 14, 4)->comment('@title=发布时间;@type=date;');
>```

+ file
> 单文件
> 样例代码
>```php
> $table->bigIncrements('file_id')->comment('@title=附件;@type=file;');
>```

+ files
> 多文件
> 样例代码
>```php
> $table->string('files', 100)->comment('@title=附件;@type=files;');
>```

+ picture
> 单图片（上传到服务器）
> 样例代码
>```php
> $table->bigIncrements('cover')->comment('@title=封面;@type=picture;');
>```

+ pictures
> 多图片（上传到服务器）
> 样例代码
>```php
> $table->string('images', 100)->comment('@title=图片集;@type=pictures;');
>```

+ richText
> 富文本
> 样例代码
>```php
> $table->text('content')->comment('@title=文章内容;@type=richText;');
>```

+ textarea
> 多行文本
> 样例代码
>```php
> $table->string('summary', 500)->comment('@title=文章摘要;@type=textarea;');
>```

+ status
> 状态 (1 表示启用或者是  0 表示禁用或者否)
> 
> 必填配置值
>
> list 
>
> DBCont中的变量名
>
> 如 Qscmf\Lib\DBCont 中的禁用启用项 $_status, 那么它的list就是 status
>
> 又如 Qscmf\Lib\DBCont 中的是否项 $_bool_status $_bool_status， 那么它的list就是 boolStatus
>
> 样例代码
>```php
> $table->tinyInteger('status')->comment('@title=状态;@type=status;@list=status; 1 启用 0 禁用');
>```

##### 生成命令
```php
php artisan qscmf:curd-gen 表名
```

执行后会自动在app/Admin/Controller和app/Common/Model下分别生成controller和model

根据业务需要，可对文件自行二次开发
