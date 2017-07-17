<?php
/* @var $this yii\web\View */

$this->title = '首页';
?>

<div class="box box-default">
    <div class="box-body">
        <div class="jumbotron text-center">
            <h1>从“心”开始!</h1>

            <p class="lead">越被嘲笑的理想，就越有被实现的价值</p>

            <p><a class="btn btn-lg btn-success" href="https://github.com/Wonail/wocenter">了解 WoCenter 最新动态</a></p>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <h2>响应迅速</h2>

                <p>Wocenter是基于php Yii2框架开发的一款优秀的高度模块化的框架，在此框架上可方便搭建后台管理系统，CMS系统等，遵循[BSD-3-Clause协议]。
                    Wocenter默认支持多语言，多主题，并默认提供一套基于AdminLTE主题的后台管理系统，整套系统众多地方使用PJAX技术，页面响应速度迅速。</p>
            </div>
            <div class="col-lg-4">
                <h2>Dispatch调度层</h2>

                <p>Wocenter在原有的MVC结构中加入了Dispatch调度层(简称D层)，用以进一步解藕细分C层，通过D层调度资源、显示页面、
                    返回相关格式结果数据给客户端，而C层则只负责路由、权限判断、提交方式合法性验证等与数据返回、页面显示无关等操作。
                </p>
            </div>
            <div class="col-lg-4">
                <h2>Service服务层</h2>

                <p>Wocenter加入了Service服务层，用于处理一些与Model层数据操作关联性不高的业务逻辑，目的在于让Model
                    层只专注于CRUD等操作，而其余的业务逻辑则交由Service层为系统或各模块提供对内开放的使用接口。</p>
            </div>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->