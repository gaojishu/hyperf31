### php环境要求
参考官网 https://hyperf.wiki/3.1/#/zh-cn/quick-start/install
```
xlswriter 扩展 需要去除exec 禁用函数
ide 提示
composer require viest/php-ext-xlswriter-ide-helper:dev-master --dev


websocket  socketio 比较老旧，暂无很好的方案
```
# 安装
```
composer install
```

# 生产环境
```
php bin/hyperf.php start
```


# 开发环境
### 安装fswatch

```
wget https://github.com/emcrisostomo/fswatch/releases/download/1.14.0/fswatch-1.14.0.tar.gz \
&& tar -xf fswatch-1.14.0.tar.gz \
&& cd fswatch-1.14.0/ \
&& ./configure \
&& make \
&& make install
```
### 运行
```
php bin/hyperf.php server:watch
```

# 测试
### 接口测试
```
composer test 全部
composer test test/Cases 指定目录
composer test test/Cases/ExampleTest.php 指定文件
composer test -- --filter=testLogin 指定方法
```

### 单元测试
```
composer test 全部
composer test test/Unit 指定目录
composer test test/Unit/ExampleTest.php 指定文件
```

# 常用命令
```
php bin/hyperf.php gen:model async_jobs --path=app/Model/Admin

php bin/hyperf.php gen:middleware CorsMiddleware



 php bin/hyperf.php gen:amqp-producer AdminActionProducer
 php bin/hyperf.php gen:amqp-consumer AdminActionConsumer


```

