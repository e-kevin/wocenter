<?php
namespace wocenter\backend\themes\adminlte\components;

use Yii;
use yii\helpers\Url;
use yii\web\Request;
use yii\web\Response;

class Dispatch extends \wocenter\core\Dispatch
{

    /**
     * 操作成功后返回结果至客户端
     *
     * @param string $message 提示信息
     * @param string|array $jumpUrl 页面跳转地址
     * @param mixed $data
     *  - 为整数，则代表页面跳转停留时间，默认为1妙，时间结束后自动跳转至指定的`$jumpUrl`页面
     *  - 为数组，则代表返回给客户端的数据
     *
     * 通常自建该方法时，建议在方法最后添加如下代码以防止不必要的输出显示
     * \Yii::$app->end();
     *
     * @return string
     */
    public function success($message = '', $jumpUrl = '', $data = [])
    {
        return $this->dispatchJump($message ?: Yii::t('wocenter/app', 'Successful operation.'), 1, $jumpUrl, $data);
    }

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
     *
     * @return string
     */
    public function error($message = '', $jumpUrl = '', $data = [])
    {
        return $this->dispatchJump($message ?: Yii::t('wocenter/app', 'Operation failure.'), 0, $jumpUrl, $data);
    }

    /**
     * 默认跳转操作，支持错误跳转和正确跳转
     * 如果`$jumpUrl`跳转地址包含`js:`字符，表示该跳转地址交由JS处理
     *
     * @param string|array $message 提示信息
     * @param integer $status 状态 1:success 0:error
     * @param string|array $jumpUrl 页面跳转地址
     * @param array|integer $data
     *
     * @return string
     */
    private function dispatchJump($message = '', $status = 1, $jumpUrl = '', $data = [])
    {
        if (!empty($jumpUrl)) {
            if (is_string($jumpUrl) && ($pos = strpos($jumpUrl, 'js:')) !== false) {
                $jumpUrl = $this->isFullPageLoad() ? '' : substr($jumpUrl, $pos + 3);
            } else {
                $jumpUrl = Url::to($jumpUrl);
            }
        }

        // 设置跳转时间
        if (is_int($data)) {
            $params['waitSecond'] = $data;
        } elseif (is_array($data)) {
            $params = $data;
            if (!isset($params['waitSecond'])) {
                $params['waitSecond'] = $status ? 1 : 3;
            }
        }
        $params['message'] = $message;
        $params['status'] = $status;

        if ($this->isFullPageLoad()) {
            $params['jumpUrl'] = $jumpUrl ?: "javascript:history.back(-1);";
            $params['header'] = $status ? Yii::t('wocenter/app', 'Success') : Yii::t('wocenter/app', 'Sorry');
            Yii::$app->getResponse()->data = $this->display('//site/dispatch', $params);
        } else {
            $params['jumpUrl'] = $jumpUrl;
            $this->controller->asJson($params);
        }

        Yii::$app->end();
    }

    /**
     * 响应式渲染视图，支持AJAX,PJAX,GET的请求方式
     *
     * 根据请求方式自动渲染视图文件，并可自动定位当前动作所属的视图文件和自动加载视图所需的模板变量
     *
     * @param string|null $view 默认根据调用此方法的调度器类名去渲染所属视图模板文件
     * @param array $assign 需要赋值的模板变量，会和已经存在的变量合并
     *
     * @return string|Response
     */
    public function display($view = null, $assign = [])
    {
        /** @var Request $request */
        $request = Yii::$app->getRequest();
        $view = $view ?: $this->view;
        $assign = array_merge($this->_assign, (array)$assign);
        if ($request->getIsAjax()) {
            if ($request->getIsPjax()) {
                // 使用布局文件并加载资源
                return $this->controller->renderContent($this->controller->renderAjax($view, $assign));
            } else {
                // 存在系统异常，则显示异常页面
                if (($exception = Yii::$app->getErrorHandler()->exception !== null)) {
                    return $this->controller->renderAjax($view, $assign);
                } else {
                    // 如果操作为只更新局部列表数据、翻页、搜索页面、数据切换时，则禁用布局文件和资源加载，直接解析视图文件
                    if (
                        $request->get('reload-list')
                        || $request->get('page')
                        || $request->get('from-search')
                        || $request->get('_toggle')
                    ) {
                        return $this->controller->renderPartial($view, $assign);
                    } else {
                        return $this->controller->renderAjax($view, $assign);
                    }
                }
            }
        } else {
            return $this->controller->render($view, $assign);
        }
    }

}