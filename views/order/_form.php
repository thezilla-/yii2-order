<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use pistol88\order\models\PaymentType;
use pistol88\order\models\ShippingType;
use pistol88\cart\widgets\ElementsList;
use pistol88\cart\widgets\CartInformer;
use pistol88\order\models\FieldValue;

$paymentTypes = ArrayHelper::map(PaymentType::find()->orderBy('order DESC')->all(), 'id', 'name');
$shippingTypes = ArrayHelper::map(ShippingType::find()->orderBy('order DESC')->all(), 'id', 'name');

pistol88\order\assets\CreateOrderAsset::register($this);
$this->registerJs("pistol88.createorder.updateCartUrl = '".Url::toRoute(['tools/cart-info'])."';");
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-lg-6">
                <div class=" panel panel-default">
                    <div class="panel-heading"><h3>Заказ</h3></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'status')->dropDownList(Yii::$app->getModule('order')->orderStatuses) ?>
                            </div>
                            <div class="col-lg-6">
                                <?= $form->field($model, 'date')->textInput(['value' => date('Y-m-d H:i:s')]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'shipping_type_id')->dropDownList($shippingTypes) ?>
                            </div>
                            <div class="col-lg-6">
                                <?= $form->field($model, 'payment_type_id')->dropDownList($paymentTypes) ?>
                            </div>
                        </div>
                        
                        <?= $form->field($model, 'comment')->textArea(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class=" panel panel-default">
                   <div class="panel-heading"><h3>Клиент</h3></div>
                   <div class="panel-body">
                       <div class="row">
                           <div class="col-lg-6">
                               <?= $form->field($model, 'user_id')->textInput(['id' => 'choose-user-id', 'data-info-service' => Url::toRoute(['tools/user-info']), 'data-toggle' => "modal", 'data-target' => "#usersModal"]) ?>
                               
                               <?= $form->field($model, 'client_name')->textInput(['maxlength' => true]) ?>

                               <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

                               <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                           </div>
                       </div>
                   </div>
               </div>
            </div>
            <div class="col-lg-6">
                <div class=" panel panel-default">
                    <div class="panel-body">
                        <div class="form-group offer">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('order', 'Create order') : Yii::t('order', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>