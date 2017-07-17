<?php
namespace wocenter\interfaces;

use yii\web\Request;
use yii\web\Response;

/**
 * 系统调度接口类
 *
 * 主要是在MVC中的C(Controller)和M(Model)之间新增一个调度层(Dispatch，简称D层)，用以进一步解藕细分C层，通过D层调度资源、显示页面、
 * 返回相关格式结果数据给客户端，而C层则只负责路由、权限判断、提交方式合法性验证等与数据返回、页面显示无关等操作。通常该做法我们建议系
 * 统采用多主题模式并且各个或个别主题需要自定义返回不同格式的结果数据、页面数据等主题相关性强的情况下使用，因为很多时候，返回的数据格
 * 式、需要调度的页面数据都和不同主题有较强的相关性（区别于一般的主题样式改变，而是页面功能和架构上的改变）。如主题A列表页面同时显示搜
 * 索表单提供数据筛查功能，而主题B的搜索表单则是通过AJAX等方式动态显示，显然B主题需要调度的页面资源变量和A主题不一样，此时如果采用同一
 * 个C层提供页面数据调度或返回相关格式结果数据的做法显然不能适用所有主题的个性化要求与设计，同时也可能给不同主题的页面提供不相关的资源
 * 数据，因此为每套主题提供专属的D层显得很有必要。并且有时面对C层复杂的动作设计，会导致C层方法量或单个动作代码过多，这与瘦控制器胖模型
 * 的设计背道而驰，而D层则可以有效地把复杂的设计解藕分离出来，针对单个动作提供专属的D层，实现一对一的关系，方便管理，同时起到瘦控制器的
 * 作用，并可使控制器与主题相关性不强，满足系统较高的可定制化需求
 *
 * @package wocenter\interfaces
 * @author E-Kevin <e-kevin@qq.com>
 */
interface DispatchInterface
{

    /**
     * 刷新列表数据操作符，由JS处理
     */
    const RELOAD_LIST = 'js:reload-list';

    /**
     * 刷新页面操作符，由JS处理
     */
    const RELOAD_PAGE = 'js:reload-page';

    /**
     * 刷新整个页面操作符，由JS处理
     */
    const RELOAD_FULL_PAGE = 'js:reload-full-page';

    /**
     * 操作成功后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * 通常自建该方法时，建议在方法最后添加如下代码以防止不必要的输出显示
     * \Yii::$app->end();
     */
    public function success($message = '', $jumpUrl = '', $data = []);

    /**
     * 操作失败后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为3妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * 通常自建该方法时，建议在方法最后添加如下代码以防止不必要的输出显示
     * \Yii::$app->end();
     */
    public function error($message = '', $jumpUrl = '', $data = []);

    /**
     * 显示页面
     *
     * @param string|null $view
     * @param array $assign
     *
     * @return Response
     */
    public function display($view, $assign = []);

    /**
     * 保存视图模板文件赋值数据
     *
     * @param string|array $key
     * @param string|array $value
     *
     * @return $this
     */
    public function assign($key, $value = '');

    /**
     * 是否全页面加载
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isFullPageLoad($request = null);

    /**
     * 执行调度，返回调度结果
     *
     * @return mixed
     */
    public function run();

    /**
     * 设置Controller和Dispatch之间需要传递的数据，一般是动作控制器需要绑定的参数
     *
     * @param string|array $key
     * @param string|null $value
     *
     * @return $this
     */
    public function setParams($key, $value = null);

}