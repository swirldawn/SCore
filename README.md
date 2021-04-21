# SCore
* 一个简单的db数据库类 仿照laravel的DB类使用方式实现 
* 但是很轻便或者说很潦草
* 此类依赖medoo 所以直接复制了他的源文件 方便本地使用
>medoo帮助文档：https://medoo.in/api/get
* 其他都是辅助的
* Log 依赖monolog 不建议使用
```
"monolog/monolog": "1.23.0",
```
# 测试使用方式
```
<?php
namespace Score;
//app的路径常量,必须
define('APPLICATION_PATH', dirname(__DIR__));

include_once("./vendor/autoload.php");
//config.php文件需要你按着config.php.example里面的格式在本地创建一个
require_once("config.php");
include_once("./vendor/swirldawn/score/src/functions.php");
//mysql 表前缀常量必须
define('TABLE_PREFIX', get_config("db.table_prefix"));

$list = \SCore\DB::table("users")->limit(1)->get();
dd($list);
```

### 常用操作

```
// 取回数据表的第一条数据
$user = DB::table('users')->where('name', 'John')->first();
DB::table('name')->first();
//获取所有
$all = DB::table('name')->get();
$all = DB::table('name')->where("age",">","20")->get();
//获取部分字段
$all = DB::table('name')->columns(['name','age'])->get();
// 插入
DB::table('users')->insert(
  ['email' => 'john@example.com', 'votes' => 0]
);

// 更新
DB::table('users')
          ->where('id', 1)
          ->update(['votes' => 1]);
// 删除
DB::table('users')->where('votes', '<', 100)->delete();

//查询sql
$list = \DB::select("select * from user");
//操作sql
$list = \DB::exec("delete  from user where id=1");
//聚合 只有三个
DB::table('users')->count();
DB::table('users')->max('age');
DB::table('users')->min('age');

```