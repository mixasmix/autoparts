<?php 
    if(empty($action)){
?>
<div class="row">
    <div class="col-md-4">
        <?php 
            if(!empty($data)){
                echo $data;
        }
        ?>
    </div>
    <div class="col-md-4">
        <?php 
            if(!empty($vins)){
        ?>
		<div id="panelcontrol_vins_info">
			<h3 style="color:#F45D10; text-align:center; margin-top: 0px;">Последние добавленные VIN</h3>
			<table>
			<?php
				foreach($vins as $vin){
					?>
						<tr>
							<td><a href="#" class="show_vin_info_table" data-toggle="modal" data-target="#myModal"><?=$vin['vin']?><div class="show_vin_info_table_div" style="display:none">
								<?php
									$info=json_decode($vin['inf']);
									if(!empty($info)){
										?>
											<table>
										<?php
											foreach($info as $k=>$v){
												?>
													<tr>
														<td><?=$k?></td>
														<td><?=$v?></td>
													</tr>
												<?php
											}
										?>
											</table>
										<?php
									}
								?>
							</div></a></td>
							<td><?=$vin['login']?></td>
						</tr>
					<?php
				}
			?>
			</table>
		</div>	
	<?php
        
            }
        
        ?>
    </div>
    <div class="col-md-4">
        <?php
		if(!empty($recalls)){
			?>
				<h3 style="color:#F45D10; text-align:center">Просят перезвонить</h3>
				<table id="recalls_status_table">
				<tr>
						<td>Телефон</td>
						<td>Комментарий</td>
						<td>Имя</td>
						<td>Дата</td>
						<td>Логин</td>
						<td>Статус</td>
						<td>Обраб</td>
						<td>Удал</td>
					</tr>
			<?php
				foreach($recalls as $r){
					
					?>
					<tr>
						<td><?=$r['phone']?></td>
						<td><?=$r['comment']?></td>
						<td><?=$r['named']?></td>
						<td><?=date("m.d.y H:i:s", $r['dt'])?></td>
						<td><a href="#"class='getuserinfo'><?=$r['login']?><div class="get_user_info_desktop" style="display:none">
							<table>
								<tr>
									<td>Имя</td>
									<td><?=(!empty($r['name']))?$r['name']:'----//----'?></td>
								</tr>
								<tr>
									<td>Фамилия</td>
									<td><?=(!empty($r['family']))?$r['family']:'----//----'?></td>
								</tr>
								<tr>
									<td>email</td>
									<td><?=(!empty($r['email']))?$r['email']:'----//----'?></td>
								</tr>
								<tr>
									<td>Дата рег.</td>
									<td><?=(!empty($r['reg_date']))?$r['reg_date']:'----//----'?></td>
								</tr>
								<tr>
									<td>Последний вход</td>
									<td><?=(!empty($r['last_active']))?$r['last_active']:'----//----'?></td>
								</tr>
								<tr>
									<td>Адрес</td>
									<td><?=(!empty($r['address']))?$r['address']:'----//----'?></td>
								</tr>
								<tr>
									<td>Профиль соц сети</td>
									<td><?=(!empty($r['social_profile']))?$r['social_profile']:'----//----'?></td>
								</tr>
								<tr>
									<td>Телефон</td>
									<td><?=(!empty($r['user_phone']))?$r['user_phone']:'----//----'?></td>
								</tr>
								<tr>
									<td>id</td>
									<td><?=(!empty($r['id_user']))?$r['id_user']:'----//----'?></td>
								</tr>
								<tr>
									<td>Отправить сообщение</td>
									<td><a href="#" class="send_message_user_info">Отправить</a></td>
								</tr>
							</table>
						</div></a></td>
						<td><?=$r['val']?></td>
						<td><a href="/panelcontrol/panelcontrol/recall/edit/<?=$r['id']?>/done">Обраб</a></td>
						<td><a href="/panelcontrol/panelcontrol/recall/edit/<?=$r['id']?>/delete">Удал.</a></td>
					</tr>
					<?php
				}
			?>
				</table>
			<?php
		}
	?>
	<br><hr>
	<div id="changelog">
		<?php
			if(!empty($log)){
				foreach($log as $l){
		?><p><?=$l['id'];?>&nbsp;<span  class="changelog_dt"><sup><?=date('d.m.y H:i:s', $l['dt']);?></sup></span>&nbsp;<span class="changelog_name"><?=$l['first_name'];?>:</span>&nbsp; <?=$l['message'];?></p><?php
				}
			}
		?>
	</div>
        
        
    </div>
</div>
<script>
$('.getuserinfo').on('click', getuserinfo_display);
$(document).click( function(event){
	  if( $(event.target).closest(".get_user_info_desktop").length ) 
        return;
	if( $(event.target).closest("a.getuserinfo").length ) 
        return;
      $(".get_user_info_desktop").hide(200);
	  c=0
      event.stopPropagation();
	   if( $(event.target).closest(".show_vin_info_table_div").length ) 
        return;
	if( $(event.target).closest("a.show_vin_info_table").length ) 
        return;
      $(".show_vin_info_table_div").hide(200);
	  c=0
      event.stopPropagation();
 });
var c=0;
 function getuserinfo_display(event){
	if(c==0){
		$(this).children('.get_user_info_desktop').toggle(200);
		c=1;
	} else{
		return false;
	}
	event.preventDefault();
}

$('.show_vin_info_table').on('click', getvininfo_display);
 function getvininfo_display(event){
	$('.modal-body').html($(this).children('.show_vin_info_table_div').html());
        /*if(c==0){
		$(this).children('.show_vin_info_table_div').toggle(200);
		c=1;
	} else{
		return false;
	}*/
	event.preventDefault();
}





$('#changelog').on('click', addChangeLog);
function addChangeLog(event){
	$('#changelog').append('<input type="text" name="msg" value="" class="form-control"/><input type="submit" class="btn btn-primary" value="Отправить"/>');
	$('#changelog').off('click', addChangeLog);
	$('#changelog input[type=submit]').on('click', changelogSubmit);
}
function changelogSubmit(event){
	
	var val=$('#changelog input[name=msg]').val();
	if(val){
		$.post('/panelcontrol/panelcontrol/changelog', {'value':val}, function(d){
			var result=JSON.parse(d);
			if(result){
				$('#changelog').children('p').first().before('<p>'+result.id+'&nbsp;<span <span  class="changelog_dt"><sup>'+result.dt+'</sup></span>&nbsp;<span  class="changelog_name">'+result.first_name+':</span>&nbsp; '+result.message+'</p>');
			}
			$('#changelog input[type=submit]').remove();
			$('#changelog input[type=text]').remove();
			$('#changelog').on('click', addChangeLog);
		});
	}
}
</script>
<?php 
    #
    # Брендлист
    #
    #
    }elseif($action=='brandlist'){
?>
    <div class="row">
<?php 
    if(!empty($brandtable)){
        echo $brandtable;
    }
?>
<?php
 
 if(!empty($nodesc)){
     echo '<h2 style="text-align:center">Бренды без описания '.count($nodesc).'</h2>';
    foreach ($nodesc as $nd){
        echo '<a href="/panelcontrol/panelcontrol/brands/brand/'.$nd['id'].'" class="art-blockcontent">'.$nd['name'].'</a> | ';
    }
}
?>
</div>
 <script>

    $('.a_table_brand_list').on('click', hideShowBrands);
    function hideShowBrands(event){
        event.preventDefault();
        $('.div_table_brand_list').hide()
        $(".div_table_brand_list").hide(200);
        $(this).parent().find('.div_table_brand_list').toggle(200);
    }

     $(document).click(function(event){

               if( $(event.target).closest(".div_table_brand_list").length ) 
                return;
               if( $(event.target).closest("a.a_table_brand_list").length ) 
                return;
              $(".div_table_brand_list").hide(200);
              event.stopPropagation();

    });

</script>
<?php
   
  }elseif($action=='brandedit'){
?>
<div id="edit_brand">
	<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
        
        </script>
        <hr>
        <form enctype="multipart/form-data">
                          <input type="file" name="file"/><input type="submit" value="ok" id="send_image"/>
        </form>
        <table id="upload_imgtable">
            <tr><th></th></tr>
        </table>
        <form method="POST" action="/panelcontrol/panelcontrol/brands/edit/<?=!empty($brand['id'])?$brand['id']:''?>"><br><br>
		<input type="text" value="<?if(!empty($brand['name'])){echo $brand['name'];}?>" name="name" placeholder="Название бренда"/><br><br>
			
		<textarea rows="10" cols="45" name="description" class="ckeditor">
		
		<?if(!empty($brand['description'])){echo $brand['description'];}?>
		</textarea><br><br>
		<br><br>
		<input type="submit" value="ОКЕЙ"/>
	</form>
</div>
<script>


$('#send_image').on('click', function(event){
    event.preventDefault();
    var fd = new FormData();
    fd.append('id', '123');
    fd.append('type', 'one');
    fd.append('upload', $('input[type=file]')[0].files[0]);

    $.ajax({
      type: 'POST',
      url: '/panelcontrol/panelcontrol/fileupload/image',
      data: fd,
      processData: false,
      contentType: false,
      dataType: "json",
      success: addimagetable,
      error: function(data) {
        console.log(data);
      }
    });
});
function addimagetable(data){
    console.log(data);
    $('#upload_imgtable').find('tr').after('<tr><td>link: /uploads/image/'+data.file_name+'</td><td><img src="/uploads/image/'+data.file_name+'" width="50" height="50"/></td></tr>');
}
</script>
<?php 

}elseif($action=='brandvotelist'){ 
    if(!empty($newVote)){
        
?>
    
<table id="brandvotelist_table">
    <tr>
            <th>Бренд</th> 
            <th>Логин</th> 
            <th>Оценка</th> 
            <th>Комментарий</th> 
            <th>Статус</th> 
            <th>Удалить</th> 
        </tr>
    <?php
    foreach($newVote as $nv){
        ?>
        <tr>
            <td><a href="/brands/brand/<?=$nv['id_brand']?>"><?=$nv['brand_name']?></a></td> 
            <td><?=!empty($nv['login'])?$nv['login']:$nv['name']?></td> 
            <td><?=$nv['vote']?></td> 
            <td class="td_commnet"><?=mb_substr($nv['comment'], 0, 50)?><span style="display:none"><?=$nv['comment']?></span></td> 
            <td><form action="/panelcontrol/panelcontrol/brandvote/edit/" method="POST"><input type="checkbox" name="moderated" value="1"><input type="hidden" name="id_comment" value="<?=$nv['id_comment']?>">Пров?<input type="submit" value="ok"></form></td> 
            <td><a href="/panelcontrol/panelcontrol/brandvote/delete/<?=$nv['id_comment']?>">Удалить</a></td> 
            
        </tr>
           <?php
    }
?>

</table>
<script>
$('td.td_commnet').on('mouseenter', function(){
    $(this).find('span').show();
})
$('td.td_commnet').on('mouseleave', function(){
    $('td.td_commnet span').hide();
})

</script>
<?php 
    }else{
        ?>
            <h2>Нет новых комментариев</h2>
        <?php
    }
}elseif($action=='pagelist'){ ?>
         <div id="page_edit_option">
	<?
	if(!empty($pages)){
		?>
			<table>
			<tr>
				<td>Название страницы</td>
				<td>Редактировать</td>
				<td>Удалить</td>
			</tr>
		<?
		foreach ($pages as $page){
			echo "<tr><td><a class='art-blockcontent' href='/page/pages/".$page['pagename']."'>".$page['title']."</a></td><td><a class='art-blockcontent' href='/panelcontrol/panelcontrol/pagedit/edit/".$page['id']."'><span class='glyphicon glyphicon glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>";
			if ($arc==0){
				echo "<td><a class='art-blockcontent' href='/panelcontrol/panelcontrol/pagedit/delete/".$page['id']."'><span class='glyphicon glyphicon glyphicon-remove-circle' aria-hidden='true'></span></a></td></tr>";
			} else {
				echo "<td><a class='art-blockcontent' href='/panelcontrol/panelcontrol/pagedit/restore/".$page['id']."'>Восст.</a></td></tr>";
			}
		}
		?>
			</table>
		<?
	}
	?>
	<a class='art-blockcontent' href='/panelcontrol/panelcontrol/pagedit/create/'>Создать новую страницу</a><br>
	<a class='art-blockcontent' href='/panelcontrol/panelcontrol/pagedit/archive/'>Страницы в архиве</a>
</div>   
<?php }elseif($action=='pagedit'){ ?>
    <div id="edit_page">
	<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
        
        </script>
	<form method="POST" action="/panelcontrol/panelcontrol/pagedit/<?if(!empty($page['id'])){echo 'edit/'.$page['id'];}else{echo 'create/';}?>"><br><br>
		<input type="text" value="<?if(!empty($page['pagename'])){echo $page['pagename'];}?>" name="pagename" placeholder="Внутреннее имя"/><br><br>
		<input type="text" value="<?if(!empty($page['title'])){echo $page['title'];}?>" name="title" placeholder="Отображаемое имя"/><br><br>
		<input type="text" value="<?if(!empty($page['description'])){echo $page['description'];}?>" name="description" placeholder="Описание страницы"/><br><br>
		
		<textarea rows="10" cols="45" name="content" class="ckeditor">
		
		<?if(!empty($page['content'])){echo $page['content'];}?>
		</textarea><br><br>
		В архив?&nbsp;<input type="checkbox" value="archived" name="arch"/>
		<br><br>
		<input type="submit" value="ОКЕЙ"/>
	</form>
    </div>
<?php }elseif($action=='userlist'){ ?>
            <div id="users_edit">
	<style>
	table{
		border-collapse:collapse;
		width:100%;
	}
	td{
		border: 1px solid black;
	}
	</style>
	<?
		if(!empty($users)){
			?>
				<table>
					<tr>
						<td>id</td>
						<td>Логин</td>
						<td>email</td>
						<td>Дата рег.</td>
						<td>Дата посл. вх.</td>
						<td>Адрес</td>
						<td>Авт. через</td>
						<td>Имя</td>
						<td>Профиль соц.</td>
						<td>Телефон</td>
						<td>Фамилия</td>
						<td>Город</td>
						<td>Нац. %</td>
						<td>Ред.</td>
						<td>Удал.</td>
					</tr>
			<?
				foreach($users as $user){
					?>
					<tr>
						<td><?=$user['id']?></td>
						<td><?=$user['login']?></td>
						<td><?=$user['email']?></td>
						<td><?=$user['reg_date']?></td>
						<td><?=$user['last_active']?></td>
						<td><?=$user['address']?></td>
						<td><?=$user['social']?></td>
						<td><?=$user['name']?></td>
						<td><?=$user['social_profile']?></td>
						<td><?=$user['phone']?></td>
						<td><?=$user['family']?></td>
						<td><?=$user['sity']?></td>
						<td><?=$user['merge']?></td>
						<td><a href="/panelcontrol/panelcontrol/users/edit/<?=$user['id']?>"><span class='glyphicon glyphicon glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>
						<td><a href="/panelcontrol/panelcontrol/users/delete/<?=$user['id']?>"><span class='glyphicon glyphicon glyphicon-remove-circle' aria-hidden='true'></span></a></td>
					</tr>
					<?
				}
			?>
				</table>
			<?
		}
	?>
</div>
<?php }elseif($action=='useredit'){ ?>
            <div id="user_edit">
<style>
input[type=text]{
	border: 1px solid #6e7d94;
    border-radius: 5px;
    color: black;
    font-size: 16px;
    height: 30px;
    text-align: left;
    width: 250px;
}
</style>
	<form method="POST" action="/panelcontrol/panelcontrol/users/<?if(!empty($user['id'])){echo 'edit/'.$user['id'];}else{echo 'create/';}?>">
	
		<table>
		
		<tr>
		<td>Введите логин</td><td><input type="text" name="login" value="<?=(!empty($user['login']))?$user['login']:''?>" placeholder="Введите логин"/></td></tr><tr>
		<td>Введите email</td><td><input type="text" name="email" value="<?=(!empty($user['email']))?$user['email']:''?>"  placeholder="Введите email"/></td></tr><tr>
		<td>Дата регистрации</td><td><input type="text" name="reg_date" value="<?=(!empty($user['reg_date']))?$user['reg_date']:''?>"  placeholder="Дата регистрации"/></td></tr><tr>
		<td>Последняя активность</td><td><input type="text" name="last_active" value="<?=(!empty($user['last_active']))?$user['last_active']:''?>"  placeholder="Последняя активность"/></td></tr><tr>
		<td>Введите адрес</td><td><input type="text" name="address" value="<?=(!empty($user['address']))?$user['address']:''?>"  placeholder="Введите адрес"/></td></tr><tr>
		<td>Зарегистрирован через</td><td><input type="text" name="auth" value="<?=(!empty($user['auth']))?$user['auth']:''?>"  placeholder="Зарегистрирован через"/></td></tr><tr>
		<td>Social</td><td><input type="text" name="social" value="<?=(!empty($user['social']))?$user['social']:''?>"  placeholder="Social"/></td></tr><tr>
		<td>Введите имя</td><td><input type="text" name="name" value="<?=(!empty($user['name']))?$user['name']:''?>"  placeholder="Введите имя"/></td></tr><tr>
		<td>Профиль соц сетей</td><td><input type="text" name="social_profile" value="<?=(!empty($user['social_profile']))?$user['social_profile']:''?>"  placeholder="Профиль соц сетей"/></td></tr><tr>
		<td>UID соц сети</td><td><input type="text" name="uid" value="<?=(!empty($user['uid']))?$user['uid']:''?>"  placeholder="UID соц сети"/></td></tr><tr>
		<td>Введите телефон</td><td><input type="text" name="phone" value="<?=(!empty($user['phone']))?$user['phone']:''?>"  placeholder="Введите телефон"/></td></tr><tr>
		<td>Введите фамилию</td><td><input type="text" name="family" value="<?=(!empty($user['family']))?$user['family']:''?>"  placeholder="Введите фамилию"/></td></tr><tr>
		<td>Введите город</td><td><input type="text" name="sity" value="<?=(!empty($user['sity']))?$user['sity']:''?>"  placeholder="Введите город"/></td></tr><tr>
		<td>Введите процент наценки</td><td><input type="text" name="merge" value="<?=(!empty($user['merge']))?$user['merge']:''?>"  placeholder="Введите процент наценки"/></td></tr><tr>
		
		
		</table>
		<input type="submit" value="Отредактировать"/>
	</form>
</div>
<?php }elseif($action=='createnews'){ ?>
            <div id="edit_news">
	<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
        
        </script>
	<form method="POST" action="/panelcontrol/panelcontrol/news/<?if(!empty($news['id'])){echo 'edit/'.$news['id'];}else{echo 'create/';}?>"><br><br>
		<input type="text" value="<?if(!empty($news['title'])){echo $news['title'];}?>" name="title" placeholder="Отображаемое имя"/><br><br>
		<input type="text" value="<?if(!empty($news['description'])){echo $news['description'];}?>" name="description" placeholder="Описание страницы"/><br><br>
		
		<textarea rows="10" cols="45" name="content" class="ckeditor">
		
		<?if(!empty($news['content'])){echo $news['content'];}?>
		</textarea><br><br>
		<br><br>
		<input type="submit" value="ОКЕЙ"/>
	</form>
</div>
<?php }elseif($action=='newslist'){ ?>
            <div id="newslist">
	<?
	if(!empty($news)){
		?>
			<table>
			<tr>
				<td>Название новсти</td>
				<td>Редактировать</td>
				<td>Удалить</td>
			</tr>
		<?
		foreach ($news as $news){
			echo "<tr><td><a class='art-blockcontent' href='/news/article/".$news['id']."'>".$news['title']."</a></td><td><a class='art-blockcontent' href='/panelcontrol/panelcontrol/news/edit/".$news['id']."'><span class='glyphicon glyphicon glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>";
			
				echo "<td><a class='art-blockcontent' href='/panelcontrol/panelcontrol/news/delete/".$news['id']."'><span class='glyphicon glyphicon glyphicon-remove-circle' aria-hidden='true'></span></a></td></tr>";
			
		}
		?>
			</table>
		<?
	}
	?>
	<a class='art-blockcontent' href='/panelcontrol/panelcontrol/news/create/'>Создать новую новость</a><br>
	
</div>
<?php }elseif ($action=='skladlist') {
   ?> <style>
    table.table_skladlist{
        width:100%;
        table-layout: fixed;
    }
    #find_partnum{
        position:relative;
    }
    #finded_parts{
        display:none;
        position: absolute;
        z-index: 100;
        background: rgb(178, 209, 245) none repeat scroll 0% 0%;
        width: 800px;
        border-radius: 10px;
        padding: 10px;
        color: black;
    }
    #part_detail{
            width: 700px;
            display:none;
            position: fixed;
            background: #FFF none repeat scroll 0% 0%;
            border: 2px solid #C0C0C0;
            border-radius: 10px;
            left: 0px;
            right: 0px;
            
            top: 70px;
            margin: auto;
            z-index:120;
            color: black;
            padding: 20px;
    }
    #part_detail input[name=count], #part_detail input[name=price]{
        width:30px;
        text-align: center;
      }
      #part_detail input[name=price]{
          width:50px;
      }
      div.sklad_part_image{
          width:200px;
          height:200px;
          margin:20px auto;
          background:red;
      }
</style>

<input type="text" value='' name='find_partnum' placeholder='Введите искомый артикул' id="find_partnum" class="form-control"/> <br><br><br>
<div id="finded_parts"></div>
<?php
    if(!empty($skladArtikuls)){
        ?>
            <table class="table_skladlist table table-striped">
                <tr>
                    <th>Артикул</th>
                    <th>Бренд</th>
                    <th>Описание</th>
                    <th>Цена</th>
                    <th>Количество на складе</th>
                </tr>
        <?php
            foreach($skladArtikuls as $sa){
                ?>
                <tr>
                    <td><?=$sa['artikul']?></td>
                    <td><?=$sa['name']?></td>
                    <td><?=$sa['description']?></td>
                    <td><?=$sa['price']?></td>
                    <td><?=$sa['count']?></td>
                </tr>
                <?php
            }
        ?>
            </table>
        <?php
    }
?>
<div id="part_detail">
  <h2>TOYOTA/LEXUS</h2>
  <h2>9031138064</h2>
  <h3>Сальник уплотнительный</h3>
  <h4></h4>
  <br><br>
  <div>Количество на складе: <input type="text" name="count" > штук</div>
  <div>Цена: <input type="text" name="price"> рублей</div>
  <div class="sklad_part_image"></div>
  <br>
  <br>
  <a href="#" class="add_sklad">Добавить на склад или сохранить изменения</a><br><a href="#" class="del_sklad">Удалить со склада</a><br>
</div>
<script>
  $('a.add_sklad').on('click', addSklad);
  $('a.del_sklad').on('click', delSklad);
  
  $(function(){
  $(document).click(function(event) {
    if ($(event.target).closest("#finded_parts").length) return;
    $("#finded_parts").hide("slow");
    if ($(event.target).closest("#part_detail").length) return;
    $("#part_detail").hide();
    event.stopPropagation();
  });
});
    $('#find_partnum').keyup(function(){
        $.post('/panelcontrol/panelcontrol/availability/getpart', {part:$('#find_partnum').val()}, function(data){
            if(data){
                var data=JSON.parse(data);
                $('#finded_parts').html('');
                $('#finded_parts').toggle();
                $('#finded_parts a').off('click');
                for(var i=0; i<data.length; i++){
                    $('#finded_parts').append('<a href="#" >'+data[i].name+' : '+data[i].artikul+'  '+data[i].description+'</a><hr>');
                    $('#finded_parts a').last().on('click', {id:data[i].id}, partDetail);
                }
            }
        });
    });
    
function partDetail(event){
    $('#finded_parts').html('');
    $('#finded_parts').toggle();
    event.preventDefault();
    var id=event.data.id;
    
    $.post('/panelcontrol/panelcontrol/availability/getpartid', {id:id}, function(data){
            if(data){
                var data=JSON.parse(data);
                    $('#part_detail').show();
                    $('#part_detail').find('h2').eq(0).text(data.name);
                    $('#part_detail').find('h2').eq(1).text(data.artikul);
                    $('#part_detail').find('h3').text(data.description);
                    $('#part_detail').find('h4').text(data.id);
                    $('#part_detail input[name=count]').val(data.count);
                    $('#part_detail input[name=price]').val(data.price);
            }
    });
}
function addSklad(event){
    event.preventDefault();
    var count=$('#part_detail input[name=count]').val();
    var price=$('#part_detail input[name=price]').val();
    var id=$('#part_detail h4').text();
    if(count>0 && price>0){
       $.post('/panelcontrol/panelcontrol/availability/addsklad', {'id':id, 'price':price, 'count':count}, function(data){
          if(data){
              $('#part_detail').html('<h2>Успешно добавлено или сохранено</h2>');
              setTimeout(function() { $('#part_detail').hide();location.reload(true);  }, 3000);
              //setTimeout(function() {}, 6000);
          }
       });
    }else if(count==0){
        delSklad(event);
    }else{
        $('#part_detail').find('h2').eq(0).html('<span style="color:red">Не указана цена или остаток на складе!</span>');
    }
}


function delSklad(event){
    event.preventDefault();
    var id=$('#part_detail h4').text();
     $.post('/panelcontrol/panelcontrol/availability/delsklad', {'id':id}, function(data){
          if(data){
              $('#part_detail').html('<h2>Позиция удалена!</h2>');
              setTimeout(function() { $('#part_detail').hide();location.reload(true);  }, 3000);
         //setTimeout(function() {}, 6000);
        }
    });
}
</script>        
      <?php  } ?>









