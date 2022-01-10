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

+ select 
> 表示该字段为外键，与另外一张表关联
> 
> 必填配置值 (table 、 list的模式二选一)
>
>  table模式（通过另外一张表获取下来选项）
>
>> table 关联的表名
>> 
>> show 关联表的描述字段
>>
>> 样例代码
>>```php
>> $table->mediumInteger('cate_id')->comment('@title=伙伴分类;@type=select;@table=qs_partner_cate;@show=title;');
>>```
>
> list模式(通过DBCont获取下拉列表)
>
>> list 
>> 
>> DBCont中的变量名
>>
>> 如 Qscmf\Lib\DBCont 中的禁用启用项 $_status, 那么它的list就是 status
>>
>> 又如 Qscmf\Lib\DBCont 中的是否项 $_bool_status $_bool_status， 那么它的list就是 boolStatus
>>
>> 样例代码
>>```php
>> $table->tinyInteger('delete_status')->comment('@title=删除状态;@type=select;@list=boolStatus; 1 启用 0 禁用');
>>```

+ radio
> list模式(通过DBCont获取下拉列表)
>
>> list
>>
>> DBCont中的变量名
>>
>> 如 Qscmf\Lib\DBCont 中的禁用启用项 $_status, 那么它的list就是 status
>>
>> 又如 Qscmf\Lib\DBCont 中的是否项 $_bool_status $_bool_status， 那么它的list就是 boolStatus
>>
>> 样例代码
>>```php
>> $table->tinyInteger('top')->comment('@title=置顶;@type=radio;@list=boolStatus; 1 是 0 否');
>>```

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
> $table->bigInteger('cover')->comment('@title=封面;@type=picture;');
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

+ url
> 网址
> 样例代码
>```php
> $table->string('url', 500)->comment('@title=网站地址;@type=url;');
>```

+ phone
> 手机号码
>
> 样例代码
>```php
> $table->string('tel', 20)->comment('@title=手机号;@type=phone;');
>```

+ email
> 邮箱地址
>
> 样例代码
>```php
> $table->string('email', 50)->comment('@title=邮箱;@type=email;');
>```

+ district
> 中国省市区地址
>
> 样例代码
>```php
> $table->integer('place')->comment('@title=住址;@type=district;');
>```

+ num
> 数字
>
> 样例代码
>```php
> $table->integer('price')->comment('@title=单价;@type=num;');
>```

3. length
> 设置字段长度限制, 第一个数字为最小长度， 第二个数字最大长度， 两个数字用逗号分隔
>
> 样例代码
>```php
> $table->string('title', 20)->comment('@title=合作伙伴;@length=1,20;');
>```

4. save
> 设置字段是否可以在list页面进行快速修改
>
> 样例代码
>```php
>$table->mediumInteger('sort')->comment('@title=排序;@save=true;');
>```
>目前支持save的类型有text、url、phone、email、num

##### 生成命令

###### 参数介绍
```text
参数
table_name 表名

选项值
mode 可选值，默认为standard
    standard: 在新页面进行新增、编辑操作，适用于业务逻辑比较复杂，表单比较重的场景
    float: 在当前页面使用模态框进行新增、编辑操作，适用于业务简单，表单比较轻的场景
```

```php
php artisan qscmf:curd-gen 表名
```

```php
php artisan qscmf:curd-gen 表名 --mode=float

// 选项可使用简写
php artisan qscmf:curd-gen 表名 -Mfloat
```

执行后会自动在app/Admin/Controller和app/Common/Model下分别生成controller和model

根据业务需要，可对文件自行二次开发
