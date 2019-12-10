<?php

/**
 * 反馈信息视图页面
 *
 * @var \yii\web\View $this
 * @var string $issueUrl
 */
?>
<?php if (YII_ENV_DEV): ?>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <p class="text-warning">
                <span class="glyphicon glyphicon-alert"></span>
                <?= Yii::t('wocenter/app',
                    'Found a bug? Tell about it to the extension developer.',
                    ['issueUrl' => $issueUrl]
                ) ?>
            </p>
        </div>
    </div>
<?php endif ?>
