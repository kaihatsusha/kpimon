<?php
/**
 * @var $this yii\web\View
 */
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
$user = Yii::$app->user->identity;
?>
<?php $this->beginContent('@app/views/layouts/base.php'); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
        <header class="main-header">
            <a href="<?php echo Url::to(['/site/index']) ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <?php echo Yii::$app->name ?>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only"><?php echo Yii::t('common', 'Toggle navigation') ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li id="timeline-notifications" class="notifications-menu">
                            <a href="<?php echo Url::to(['/site/index']) ?>">
                                <i class="fa fa-bell"></i>
                                <span class="label label-success">
                                    0
                                </span>
                            </a>
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        <li id="log-dropdown" class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-warning"></i>
                            <span class="label label-danger">
                                0
                            </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header"><?php echo Yii::t('common', 'You have {num} log items', ['num'=>  0]) ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        
                                            <li>
                                                <a href="<?php echo Url::to(['/admin/log/view']) ?>">
                                                    <i class="fa fa-warning text-red"></i>
                                                    Log 1
                                                </a>
                                            </li>
											<li>
                                                <a href="<?php echo Url::to(['/admin/log/view']) ?>">
                                                    <i class="fa fa-warning text-yellow"></i>
													Log 2
                                                </a>
                                            </li>
                                    </ul>
                                </li>
                                <li class="footer">
                                    <?php echo Html::a(Yii::t('common', 'View all'), ['/admin/log/index']) ?>
                                </li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?= Yii::$app->homeUrl?>img/anonymous.jpg" class="user-image">
                                <span><?= $user->userProfile->getFullName()?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header light-blue">
                                    <img src="<?= Yii::$app->homeUrl?>img/anonymous.jpg" class="img-circle" alt="User Image" />
                                    <p>
                                        <?= $user->userProfile->getFullName()?>
                                        <small>
                                            <?php echo Yii::t('common', 'Member since {0, date, short}', $user->created_at) ?>
                                        </small>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <?php echo Html::a(Yii::t('common', 'Profile'), ['/account/profile'], ['class'=>'btn btn-default btn-flat']) ?>
                                    </div>
                                    <div class="pull-left">
                                        <?php echo Html::a(Yii::t('common', 'Account'), ['/account/account'], ['class'=>'btn btn-default btn-flat']) ?>
                                    </div>
                                    <div class="pull-right">
                                        <?php echo Html::a(Yii::t('common', 'Logout'), ['/site/logout'], ['class'=>'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?= Yii::$app->homeUrl?>img/anonymous.jpg" class="img-circle" />
                    </div>
                    <div class="pull-left info">
                        <p><?php echo Yii::t('common', 'Hello, {username}', ['username'=>  $user->userProfile->getFullName()]) ?></p>
                        <a href="<?php echo Url::to(['/account/profile']) ?>">
                            <i class="fa fa-circle text-success"></i>
                            <?php echo Yii::$app->formatter->asDatetime($user->created_at) ?>
                        </a>
                    </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php echo app\widgets\Menu::widget([
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
                    
                ]) ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php echo $this->title ?>
                    <?php if (isset($this->params['subtitle'])): ?>
                        <small><?php echo $this->params['subtitle'] ?></small>
                    <?php endif; ?>
                </h1>

                <?php echo Breadcrumbs::widget([
                    'tag'=>'ol',
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </section>

            <!-- Main content -->
            <section class="content">
                <?php if (Yii::$app->session->hasFlash('alert')):?>
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ])?>
                <?php endif; ?>
                <?php echo $content ?>
            </section><!-- /.content -->
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php $this->endContent(); ?>