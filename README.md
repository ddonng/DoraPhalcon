# DoraPhalcon

标签（空格分隔）：swoole doraRPC phalcon

---

这是一个菜鸟的好时代，因为大神们不断的贡献……好人一生平安！(●ˇ∀ˇ●)

DoraPhalcon只是将DoraRPC与Phalcon组装在一起……DoraRPC写得非常好，作者[@蓝天](http://weibo.com/thinkpc)还在继续按照他最初的完美设计在不断完成，所以[DoraRPC](https://github.com/xcl3721/Dora-RPC)还可以更好o(^▽^)o，顺便带上DoraPhalcon。用Phalcon是因为太喜欢它的DI了，而且用习惯了后也不想再换框架……要我自己写，水平不够，轮子造不出来( ▼-▼ )……


DoraPhalcon上PHP7，目前：
> *  PHP7已发布
> *  Swoole支持PHP7
> *  Phalcon3.0发布
> * [Waiting] ODM上php7需要等待官方

## Install
1、安装phalcon3（最新）

2、安装Dora-RPC

    composer require "xcl3721/dora-rpc"

## 如何体验
准备了docker环境，按以下步骤可进行体验：

1、安装docker
这里访问[docker官网][1]，安装好对应系统的

2、下载代码库

    git clone https://github.com/ddonng/DoraPhalcon.git
3、编译镜像

    cd DoraPhalcon/tests
    docker build -t ddonng/rpc:7.0 .
4、启动容器
修改docker-compose.yml中的HOST_MACHINE_IP为当前电脑的ip，然后命令：

    docker-compose up -d

由于数据库还未建立，运行会报错。

5、导入sql文件
使用mysql管理软件，如mysql-workbench，连接到localhost:3306，创建数据库core，并导入tests目录下的dora.sql创建数据表及测试数据。

6、重新启动服务

    docker-compose down
    #全部删除后
    docker-compose up -d

7、尽情体验

  [1]: https://www.docker.com
