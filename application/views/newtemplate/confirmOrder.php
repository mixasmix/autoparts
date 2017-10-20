<div class="container">
    <h2 class="align_center"><?=$this->lang->line('template_confirm_order_title')?></h2>
    <form action="/mybacket/done" method="POST" class="confirm_order_form align_center_block">
        <input type="text" name="phone" class="form-control align_center" placeholder="<?=$this->lang->line('template_confirm_order_phone_placeholder')?>">
        <br>
        <input type="text" name="comment"  class="form-control confirm_order_form_comment align_center" placeholder="<?=$this->lang->line('template_confirm_order_comment')?>">
        <br>
        <input type="submit"  class="search-block__submit input-button btn" value="<?=$this->lang->line('template_confirm_order_description')?>"/>
    </form>
</div>