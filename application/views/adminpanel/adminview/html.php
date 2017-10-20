<!doctype html>
<html>
    <head>
        <title>Админпанель</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="/css/panel_style_new.css" rel="stylesheet">
        <link rel="stylesheet" href="/css/newtemplate/font-awesome.min.css">
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="/js/admin_script.js"></script>
    </head>
    <body>
        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">
               
                <div class="modal-body">
                  <p>One fine body&hellip;</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class='container'>
                <div class="navbar-inner">
                <a class="navbar-brand" href="/panelcontrol/">Админпанель</a>
                  <ul class="nav nav-pills">
                    
                    <li>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        Заказы <span class="caret"></span>
                      </a>
                        <ul class="dropdown-menu">
                           <li><a href="/panelcontrol/panelcontrol/zakaz" id="zakaz_button">Активные заказы</a></li>
                           <li><a href="/panelcontrol/panelcontrol/zakaz/archive" id="zakaz_button">Архив заказов</a></li>
                           <li><a href="/panelcontrol/panelcontrol/inworkdelivery" id="send_providers_button">Отправить заказы поставщику</a></li> 
                        </ul>
                    </li>
                    
                    <li><a href="/panelcontrol/panelcontrol/brandvote" id="barndvote_button">Новые комментарии</a></li>
                    <li><a href="/panelcontrol/panelcontrol/pagedit" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Управление контентом <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                           <li><a href="/panelcontrol/panelcontrol/pagedit" id="pagedit_button">Управление страницами</a></li>
                           <li><a href="/panelcontrol/panelcontrol/news">Управление новостями </a></li>
                        </ul>
                    </li>
                    <li><a href="/panelcontrol/panelcontrol/users" id="users_button">Управление пользователями</a></li>
                    <li><a href="/panelcontrol/panelcontrol/brands" id="brands_button">Бренды</a></li>
                    <li><a href="/panelcontrol/panelcontrol/acessory" id="brands_button"  data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Аксессуары  <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                           <li><a href="/panelcontrol/panelcontrol/acessory/categories">Управление категориями </a></li>
                           <li><a href="/panelcontrol/panelcontrol/acessory/acessories">Управление аксессуарами </a></li>
                        </ul>
                    </li>
                    <li><a href="/panelcontrol/panelcontrol/availability" id="brands_button">Склад</a></li>
                    <li><a href="/panelcontrol/panelcontrol/out" id="out_button"> <span class="glyphicon glyphicon glyphicon-log-out" aria-hidden="true"></span></a></li>
                  </ul>
                </div>
            </div>
        </div>
        <div class="container" id="content-container">
            <?=$content?>
        </div>
    </body>
</html>