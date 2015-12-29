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
				'label'=>Yii::t('common', 'SYSTEM'),
				'options'=>['class'=>'header'],
				'type'=>'split'
			],
			[
				'label'=>Yii::t('common', 'Home'),
				'url'=>['/site/index'],
				'icon'=>'<i class="fa fa-home"></i>'
			],
			[
				'label'=>Yii::t('common', 'FINANCES'),
				'options'=>['class'=>'header'],
				'type'=>'split'
			],
			[
				'label'=>Yii::t('fin.menuLeft', 'Account'),
				'url'=>['/fin/account/index'],
				'icon'=>'<i class="fa fa-money"></i>',
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
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Edit'),
						'url'=>['/fin/payment/update'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Copy'),
						'url'=>['/fin/payment/copy'],
						'requireId'=>true,
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
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Create'),
						'url'=>['/fin/deposit/create'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'View'),
						'url'=>['/fin/deposit/view'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Edit'),
						'url'=>['/fin/deposit/update'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Copy'),
						'url'=>['/fin/deposit/copy'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			],
			[
				'label'=>Yii::t('fin.menuLeft', 'Interest Unit'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-rocket"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('fin.menuLeft', 'List'),
						'url'=>['/fin/interest/index'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Create'),
						'url'=>['/fin/interest/create'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'View'),
						'url'=>['/fin/interest/view'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Edit'),
						'url'=>['/fin/interest/update'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Copy'),
						'url'=>['/fin/interest/copy'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			],
			[
				'label'=>Yii::t('fin.menuLeft', 'Report'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-bar-chart-o"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('fin.menuLeft', 'Payment'),
						'url'=>['/fin/report/payment'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Deposit'),
						'url'=>['/fin/report/deposit'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('fin.menuLeft', 'Assets'),
						'url'=>['/fin/report/assets'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			],
			[
				'label'=>Yii::t('common', 'INTERNET'),
				'options'=>['class'=>'header'],
				'type'=>'split'
			],
			[
				'label'=>Yii::t('net.menuLeft', 'Bill'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-edit"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('net.menuLeft', 'List'),
						'url'=>['/net/bill/index'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'Create'),
						'url'=>['/net/bill/create'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'View'),
						'url'=>['/net/bill/view'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'Edit'),
						'url'=>['/net/bill/update'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			],
			[
				'label'=>Yii::t('net.menuLeft', 'Payment'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-edit"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('net.menuLeft', 'List'),
						'url'=>['/net/payment/index'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'Create'),
						'url'=>['/net/payment/create'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'View'),
						'url'=>['/net/payment/view'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'Edit'),
						'url'=>['/net/payment/update'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			],
			[
				'label'=>Yii::t('net.menuLeft', 'Customer'),
				'url'=>['#'],
				'icon'=>'<i class="fa fa-edit"></i>',
				'right-icon'=>'<i class="fa fa-angle-left pull-right"></i>',
				'items'=>[
					[
						'label'=>Yii::t('net.menuLeft', 'List'),
						'url'=>['/net/customer/index'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'Create'),
						'url'=>['/net/customer/create'],
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'View'),
						'url'=>['/net/customer/view'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					],
					[
						'label'=>Yii::t('net.menuLeft', 'Edit'),
						'url'=>['/net/customer/update'],
						'requireId'=>true,
						'icon'=>'<i class="fa fa-circle-o"></i>'
					]
				]
			]
		]
	]); ?>
 </section></aside><!-- /.sidebar -->   