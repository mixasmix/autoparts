<?php
/* echo '<pre>';
  var_dump($orders); exit; /* */
if (empty($orders)) {
    ?>
    <h2 class="align-center">Нет заказов</h2>
    <?php
} else {
    ?>
    <table class="table">
        <tr>
            <th>Дата</th>
            <th>№</th>
            <th>Пользователь</th>
            <th>Кол-во позиций</th>
            <th>Сумма опт</th>
            <th>Сумма розн</th>
            <th>Прибыль</th>
            <th>Комментарий</th>
            <th>Адрес доставки</th>
            <th>Тлф</th>
            <th>Статус</th>
            <th>Управление заказом</th>
            <th>Примечание</th>
            <?php
            foreach ($orders as $o) {
                ?>
            <tr>
                <td><?= date('d.m.y h:i:s', $o['time']) ?></td>
                <td><?= $o['id'] ?></td>
                <td><?= $o['username'] ?></td>
                <td><?= $o['quant'] ?></td>
                <td><?= $o['whoes_cost'] ?>руб</td>
                <td><?= $o['retail_cost'] ?>руб</td>
                <td><?= $o['profit'] ?>руб</td>
                <td><?= $o['comment'] ?></td>
                <td><?= (!empty($this->aauth->get_user_var('addres', $o['user_id']))) ? $this->aauth->get_user_var('addres', $o['user_id']) : '' ?></td>
                <td><?= (!empty($o['phone'])) ? $o['phone'] : ((!empty($this->aauth->get_user_var('phone', $o['user_id']))) ? $this->aauth->get_user_var('phone', $o['user_id']) : '') ?></td>
                <td>

                </td>
                <td>
                    <a href="#Modal<?= $o['id'] ?>" role="button" data-toggle="modal" class="order_table_manage_link__information_order">
                        <span class="fa fa-info-circle order_table_manage_icon"></span>
                    </a>
                    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="Modal<?= $o['id'] ?>">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <span class="modal-title">Заказ #<?= $o['id'] ?> от <?= date('d.m.y h:i:s', $o['time']) ?></span> <div class="form-group">
                                        <label for="statusSelect">Статус заказа:</label>
                                        <select id="statusSelect" class="form-control selectpicker" name="status" data-order_id="<?= $o['id'] ?>">
                                            <?php
                                            if (!empty($statuses)) {
                                                foreach ($statuses as $status) {
                                                    ?>
                                                    <option value="<?= $status['id'] ?>" <?= ($status['id'] === $o['id_status']) ? 'selected="selected"' : '' ?>><?= $status['name'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <tr>
                                            <th><input type="checkbox" name="checkedAll"/></th>
                                            <th>ID</th>
                                            <th>Артикул</th>
                                            <th>Бренд</th>
                                            <th>Описан.</th>
                                            <th>Дост.</th>
                                            <th>Цена опт.</th>
                                            <th>Цена розн.</th>
                                            <th>Кол-во</th>
                                            <th>Поставщик</th>
                                            <th>Статус</th>
                                        </tr>
                                        <?php
                                        foreach ($o['order_list'] as $ol) {
                                            ?>
                                            <tr class="row_id_<?= $ol['id'] ?>">
                                                <td><input type="checkbox" class="order_list_checkbox" name="check_position_input" value="<?= $ol['id'] ?>"></td>
                                                <td><?= $ol['id'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="text" data-content_lenght="<?= strlen($ol['artikul']) ?>" data-name_field="artikul"><?= $ol['artikul'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="text" data-content_lenght="<?= strlen($ol['brand']) ?>" data-name_field="brand"><?= $ol['brand'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="text" data-content_lenght="<?= strlen($ol['description']) ?>" data-name_field="description"><?= $ol['description'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="text" data-content_lenght="<?= strlen($ol['delivery']) ?>" data-name_field="delivery"><?= $ol['delivery'] ?></td>
                                                <td><?= $ol['supplier_price'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="number" data-content_lenght="<?= strlen($ol['price']) ?>" data-name_field="price"><?= $ol['price'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="number" data-content_lenght="<?= strlen($ol['quantity']) ?>" data-name_field="quantity"><?= $ol['quantity'] ?></td>
                                                <td title="Для изменения значения поля сделайте двойной щелчок" class="edited_information" data-position_id="<?= $ol['id'] ?>" data-field_type="text" data-content_lenght="<?= strlen($ol['provider']) ?>" data-name_field="provider"><?= $ol['provider'] ?></td>
                                                <td>
                                                    <select name="order_item_status" class="form-control" data-position_id="<?= $ol['id'] ?>">
                                                        <?php
                                                        if (!empty($statuses)) {
                                                            foreach ($statuses as $status) {
                                                                ?>
                                                                <option value="<?= $status['id'] ?>" <?= ($status['id'] === $ol['id_status']) ? 'selected="selected"' : '' ?>><?= $status['name'] ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                        ?> 
                                    </table>
                                    <hr>
                                    <a href="#" id="dLabel<?= $o['id'] ?>" data-target="#"  title="Добавить позицию" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" ><span class="fa order_table_manage_icon fa-plus"></span></a>
                                    <ul class="dropdown-menu" aria-labelledby="dLabel<?= $o['id'] ?>">
                                        <li><a href="#" data-toggle="modal" data-target="#modal-backet" class="select_position_from_backet">Из корзины</a></li>
                                        <li><a href="#" data-toggle="modal" data-target="#modal-new_position" class="add_position_manual">Ввести данные вручную</a></li>
                                    </ul>
                                    <span class="order_table_manage_icon">/</span>
                                    <a href="#" title="Удалить позицию" class="deleted_checked_position"><span class="fa order_table_manage_icon fa-minus"></span></a>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td></td>
            </tr>
            <?php
        }
        ?>
    </tr>
    </table>
    <?php
}
?>
<div class="modal fade" id="modal-backet">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"> 
                <button class="close" type="button" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Выберите позиции из корзины</h4>

            </div>
            <div class="modal-body">
                <?php
                if (!empty($backets)) {
                    ?>
                    <table class="table">
                        <tr>
                            <th><input type="checkbox" name="checkedAll"/></th>
                            <th>ID</th>
                            <th>Артикул</th>
                            <th>Бренд</th>
                            <th>Описан.</th>
                            <th>Дост.</th>
                            <th>Цена опт.</th>
                            <th>Цена розн.</th>
                            <th>Кол-во</th>
                        </tr>
                        <?php
                        foreach ($backets as $backet) {
                            ?>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td><?= $backet['id'] ?></td>
                                <td><?= $backet['artikul'] ?></td>
                                <td><?= $backet['brand'] ?></td>
                                <td><?= $backet['description'] ?></td>
                                <td><?= $backet['delivery'] ?></td>
                                <td><?= $backet['supplier_price'] ?></td>
                                <td><?= $backet['price'] ?></td>
                                <td><?= $backet['quantity'] ?></td>

                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                }
                ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>    
</div>    

<div class="modal fade" id="modal-new_position">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"> 
                <button class="close" type="button" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title">Создание новой позиции</h4>

            </div>
            <div class="modal-body">
                <form action="#" class="form-horizontal">

                    <div class="form-group">
                        <label for="input_new_position1" class="col-md-2 control-label">Артикул</label>        
                        <div class="col-md-3">
                            <input id="input_new_position1"type="text" value="" name="new_art" class="new_art form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_new_position2" class="col-md-2 control-label">Бренд</label>        
                        <div class="col-md-3">
                            <input id="input_new_position2"type="text" value="" name="new_brand" class="new_brand form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_new_position3" class="col-md-2 control-label">Описание</label>        
                        <div class="col-md-3">
                            <input id="input_new_position3"type="text" value="" name="new_desc" class="new_desc form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_new_position4" class="col-md-2 control-label">Доставка</label>        
                        <div class="col-md-3">
                            <input id="input_new_position4"type="text" value="" name="new_deliv" class="new_deliv form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_new_position5" class="col-md-2 control-label">Цена поставщика</label>        
                        <div class="col-md-3">
                            <input id="input_new_position5"type="text" value="" name=new_price_suppl"" class="new_price_suppl form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_new_position6" class="col-md-2 control-label">Цена</label>        
                        <div class="col-md-3">
                            <input id="input_new_position6"type="text" value="" name="new_price" class="new_price form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_new_position7" class="col-md-2 control-label">Количесво</label>        
                        <div class="col-md-3">
                            <input id="input_new_position7"type="number" size="2" value="1" name="new_quant" class="new_quant form-control"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="button">Сохранить</button>
                <button class="btn btn-primary" type="button" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>    
</div>       