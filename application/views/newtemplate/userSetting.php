<div class="container">
    <h3><?= $this->lang->line('template_user_setting_person_data_header') ?></h3>
    <hr>
    <form action="#" class="form-horizontal" role="form">
        <div class="form-group">
            <label for="email" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_email') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('email'))) ? $this->aauth->get_user_var('email') : $this->lang->line('template_user_setting_form_label_email') ?>" id="email" name="email" disabled>
            </div>
        </div>
        <div class="form-group">
            <label for="family" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_family') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('family'))) ? $this->aauth->get_user_var('family') : $this->lang->line('template_user_setting_form_label_family') ?>" id="family" name="family">
            </div>
        </div>
        <div class="form-group">
            <label for="name" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_name') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('name'))) ? $this->aauth->get_user_var('name') : $this->lang->line('template_user_setting_form_label_name') ?>" id="name" name="name">
            </div>
        </div>
        <div class="form-group">
            <label for="grandname" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_grandname') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('grandname'))) ? $this->aauth->get_user_var('grandname') : $this->lang->line('template_user_setting_form_label_grandname') ?>" id="grandname" name="grandname">
            </div>
        </div>
        <div class="form-group">
            <label for="phone" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_phone') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('phone'))) ? $this->aauth->get_user_var('phone') : $this->lang->line('template_user_setting_form_label_phone') ?>" id="phone" name="phone">
            </div>
        </div>
        <div class="form-group">
            <label for="city" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_city') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('city'))) ? $this->aauth->get_user_var('city') : $this->lang->line('template_user_setting_form_label_city') ?>" id="city" name="city">
            </div>
        </div>
        <div class="form-group">
            <label for="addres" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_form_label_addres') ?></label>
            <div class="col-md-3">
                <input type="text" class="form-control user_data_input" placeholder="<?= (!empty($this->aauth->get_user_var('addres'))) ? $this->aauth->get_user_var('addres') : $this->lang->line('template_user_setting_form_label_addres') ?>" id="addres" name="addres">
            </div>
        </div>
    </form>
    <h3><?= $this->lang->line('template_user_setting_replace_password_header') ?></h3>
    <hr>
    <form action="#"  class="form-horizontal" role="form">
        <div class="form-group">
            <label for="new_password" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_new_password_label') ?></label>
            <div class="col-md-3">
                <input type="password" class="form-control" placeholder="<?= $this->lang->line('template_user_setting_new_password_label') ?>" id="new_password" name="new_password">
            </div>
        </div><div class="form-group">
            <label for="confirm_password" class="col-md-2 control-label"><?= $this->lang->line('template_user_setting_confirm_password_label') ?></label>
            <div class="col-md-3">
                <input type="password" class="form-control" placeholder="<?= $this->lang->line('template_user_setting_confirm_password_label') ?>" id="confirm_password" name="confirm_password">
            </div>
        </div>
        <span class="hide pass_not_ident"><?= $this->lang->line('template_user_setting_password_no_ident') ?></span>
        <div class='add_backet_btn btn newpass_btn' disabled="disabled"><?= $this->lang->line('template_user_setting_replace_password_header') ?></div>
        <div class="alert alert-warning pass_confirm hide"><?= $this->lang->line('template_user_setting_password_confirm') ?></div>
        <div class="alert alert-danger pass_error hide"><?= $this->lang->line('template_user_setting_password_error') ?></div>
    </form>
    


    <!--скрипт личного кабинета пользователя-->
    <script src="/js/user.js"></script>
</div>