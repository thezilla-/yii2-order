<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

$this->title = Yii::t('order', 'Order').' #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">
    <?php if(Yii::$app->session->hasFlash('reSendDone')) { ?>
        <script>
        alert('<?= Yii::$app->session->getFlash('reSendDone') ?>');
        </script>
    <?php } ?>

    <p>
        <?= Html::a(Yii::t('order', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('order', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('order', 'Realy?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?=pistol88\order\widgets\ChangeStatus::widget(['model' => $model]);?>
    
    <?php
    $detailOrder = [
        'model' => $model,
        'attributes' => [
            'id',
			[
				'attribute' => 'date',
				'value'		=> date(yii::$app->getModule('order')->dateFormat, $model->timestamp),
			],
        ],
    ];
    
    if($model->client_name) {
        $detailOrder['attributes'][] = 'client_name';
    }
    
    if($model->phone) {
        $detailOrder['attributes'][] = 'phone';
    }

    if($model->email) {
        $detailOrder['attributes'][] = 'email:email';
    }
    
    if($model->promocode) {
        $detailOrder['attributes'][] = 'promocode';
    }

    if($model->comment) {
        $detailOrder['attributes'][] = 'comment';
    }
    
    if($model->payment_type_id && isset($paymentTypes[$model->payment_type_id])) {
        $detailOrder['attributes'][] = [
            'attribute' => 'payment_type_id',
            'value'		=> @$paymentTypes[$model->payment_type_id],
        ];
    }
    
    if($model->shipping_type_id && isset($shippingTypes[$model->shipping_type_id])) {
			$detailOrder['attributes'][] = [
				'attribute' => 'shipping_type_id',
				'value'		=> $shippingTypes[$model->shipping_type_id],
			];
    }

    if($model->delivery_type == 'totime') {
        $detailOrder['attributes'][] = 'delivery_time_date';
        $detailOrder['attributes'][] = 'delivery_time_hour';
        $detailOrder['attributes'][] = 'delivery_time_min';
    }

    if($fields = $fieldFind->all()) {
        foreach($fields as $fieldModel) {
            $detailOrder['attributes'][] = [
				'label' => $fieldModel->name,
				'value'		=> Html::encode($fieldModel->getValue($model->id)),
			];
        }
    }

    if($model->seller) {
        $detailOrder['attributes'][] = [
            'label' => yii::t('order', 'Seller'),
            'value' => Html::encode($model->seller->name),
        ];
    }

    echo DetailView::widget($detailOrder);
    ?>

	<h2><?=Yii::t('order', 'Order list'); ?></h2>

    <?= \kartik\grid\GridView::widget([
        'export' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'attribute' => 'item_id',
                'filter' => false,
                'content' => function($model) {
                    if($productModel = $model->product) {
                        return $productModel->getCartId().'. '.$productModel->getCartName();
                    }
                    else {
                        return Yii::t('order', 'Unknow product');
                    }
                }
			],
			[
				'attribute' => 'description',
                'filter' => false,
                'content' => function($model) {
                    $elementOptions = json_decode($model->options);
					$productOptions = $model->getModel()->getCartOptions();

					$return = $model->description;
					
                    if($elementOptions) { 
                        foreach($productOptions as $optionId => $optionData) {
							foreach($elementOptions as $id => $value) {
								if($optionId == $id) {
									if(!$variantValue = $optionData['variants'][$value]) {
										$variantValue = 'deleted';
									}
									$return .= Html::tag('p', Html::encode($optionData['name']).': '.Html::encode($variantValue).';');
								}
							}
                            
                        }
                    }
                    
                    return $return;
                }
			],
			'count',
			'base_price',
			['attribute' => 'price', 'label' => yii::t('order', 'Price').' %'],
			[
				'label' => yii::t('order', 'Write-off'),
				'content' => function($model) {
					return '';
				}
			],
            ['class' => 'yii\grid\ActionColumn', 'controller' => '/order/element', 'template' => '{delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 75px;']],
        ],
    ]); ?>
    <h3 align="right">
            <?=Yii::t('order', 'In total'); ?>:
            <?=$model->count;?> <?=Yii::t('order', 'on'); ?>
            <?=$model->cost;?>
            <?=Yii::$app->getModule('order')->currency;?>
            <?php if($model->promocode) { ?>
                (<?=yii::t('order', 'Discount');?> <?=$model->promocode;?><?php if(yii::$app->has('promocode') && $code = yii::$app->promocode->checkExists($model->promocode)) { echo " {$code->discount}%"; } ?>)
            <?php } ?>
    </h3>
</div>
