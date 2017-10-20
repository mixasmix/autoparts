<?php
    if(!empty($backetPositions)){
?>
<table class="table">
    <tr>
        <th><?=$this->lang->line('template_tablesearch_artikul')?></th>
        <th><?=$this->lang->line('template_tablesearch_brand')?></th>
        <th><?=$this->lang->line('template_tablesearch_description')?></th>
        <th><?=$this->lang->line('template_tablesearch_quantity')?></th>
        <th><?=$this->lang->line('template_tablesearch_price')?></th>
        <th><?=$this->lang->line('template_tablesearch_days_delivery')?></th>
        <th><?=$this->lang->line('template_delete_label')?></th>
    </tr>
    <?php 
        foreach($backetPositions as $position){
    ?>
        
            <tr>
                <td><?=$position['artikul']?></td>
                <td><?=$position['brand']?></td>
                <td><?=$position['description']?></td>
                <td><?=$position['quantity']?></td>
                <td><?=$position['price']?></td>
                <td><?=$position['delivery']?></td>
                <td><form action="/mybacket/delete" method="POST"><input type="submit" class="add_backet_btn btn" value="<?=$this->lang->line('template_delete_label')?>"/><input type="hidden" name="position_id" value="<?=$position['id']?>"/></form></td>
            </tr>
        
    <?php 
        }
    ?>
</table>
<hr/>
<div class="row">
    <div class="col-md-6">
        <form action="/mybacket/confirm" method="POST">
            <input type="submit" class="add_backet_btn btn align_center_block" value="<?=$this->lang->line('template_backet_to_order')?>"/>
            <input type="hidden" name="tootder" value="1"/>
        </form>
    </div>
    <div class="col-md-6">
        <form  action="/mybacket/clear"  method="POST">
            <input type="submit" class="invert_btn btn align_center_block" value="<?=$this->lang->line('template_backet_clear')?>" />
            <input type="hidden" name="clear" value="1"/>
       </form>
    </div>
</div>
<?php
    }else{
?>
<h2 class="align_center"><?=$this->lang->line('template_backet_empty')?></h2>
<?php
    }
?>