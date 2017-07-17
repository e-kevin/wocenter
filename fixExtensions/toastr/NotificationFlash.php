<?php
namespace lavrentiev\widgets\toastr;

class NotificationFlash extends Notification
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        $session = \Yii::$app->session;

        $flashes = $session->getAllFlashes();

        foreach ($flashes as $type => $data) {

            $data = (array)$data;

            foreach ($data as $i => $message) {

                Notification::widget(['type' => $type, 'message' => $message, 'options' => $this->options]); // 主要修复该行

            }

            $session->removeFlash($type);
        }
    }
}
