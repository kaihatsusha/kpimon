<?php
	use yii\helpers\Url;
?>

<!-- sidebar: style can be found in sidebar.less -->
<aside class="main-sidebar"><section class="sidebar">
	<!-- Sidebar user panel -->
	<div class="user-panel">
		<div class="pull-left image"><img src="<?= Yii::$app->homeUrl?>img/anonymous.jpg" class="img-circle" /></div>
		<div class="pull-left info">
			<p><?php echo Yii::t('common', 'Hello, {username}', ['username'=>  $user->userProfile->getFullName()]) ?></p>
			<a href="<?php echo Url::to(['/account/profile']) ?>">
				<i class="fa fa-circle text-success"></i>
				<?php echo Yii::$app->formatter->asDatetime($user->created_at) ?>
			</a>
		</div>
	</div>
	<!-- sidebar menu: : style can be found in sidebar.less -->
	<?= app\widgets\Menu::widget([
		'options'=>['class'=>'sidebar-menu'],
		'labelTemplate' => '<a href="#">{icon}<span>{label}</span>{right-icon}{badge}</a>',
		'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
		'submenuTemplate'=>"\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
		'activateParents'=>true,
		'items'=>[
			[
				'label'=>Yii::t('common', 'Home'),
				'url'=>['/site/index'],
				'icon'=>'<i class="fa fa-bar-chart-o"></i>'
			],
			[
				'label'=>Yii::t('fin.menuLeft', 'Account'),
				'url'=>['/fin/account/index'],
				'icon'=>'<i class="fa fa-files-o"></i>',
			],
			[
				'label'=>Yii::t('fin.menuLeft', 'Payment'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-edit"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('fin.menuLeft', 'List'),
						'url'=>['/fin/payment/index'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Create'),
						'url'=>['/fin/payment/create'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'View'),
						'url'=>['/fin/payment/view'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Edit'),
						'url'=>['/fin/payment/update'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Copy'),
						'url'=>['/fin/payment/copy'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			],
			[
				'label'=>Yii::t('fin.menuLeft', 'Deposit'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-bank"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('fin.menuLeft', 'List'),
						'url'=>['/fin/deposit/index'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			]
		]
	]); ?>
 </section></aside><!-- /.sidebar -->   