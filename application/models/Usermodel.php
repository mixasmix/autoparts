<?php

/**
 * @author Mixa
 * @version 1.0
 * Модель для работы с данными пользователя
 */
class UserModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        if(!$this->aauth->is_loggedin()){
                $this->aauth->login_fast(4);
        }
    }

    /**
     * Метод изменяет пользовательские дпнные, которые пришли по AJAX
     * @return bool
     */
    public function editUserData() {
        if (!$this->aauth->is_login()) {
            header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
            exit;
        }

        if ($this->input->post('action') == 'edituserinfo') {
            echo $this->aauth->set_user_var($this->input->post('lk_param'), $this->input->post('value'));
            exit;
        }
    }

    /**
     * Сменить пароль пользователя
     */
    public function rePass() {
        if (!$this->aauth->is_login()) {
            header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
            exit;
        }

        if ($this->input->post('action') == 'replase_password') {

            if ($this->input->post('new_pass') == $this->input->post('conf_pass')) {
                $res = $this->aauth->update_user($this->aauth->get_user_id(), false, $this->input->post('new_pass'));
                if($res)
                    $this->aauth->logout();
                
            } else {
                $res = false;
            }
            echo $res;
            exit;
        }
    }

}
