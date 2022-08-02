<?php

namespace G28\Eucapacito\Options;

class MessageOptions
{
    const OPTIONS_NAME                  = 'eucapacito-webapp';
    const OPTIONS_GROUP                 = 'message_group';
    const REGISTER_ERROR                = 'register_error';
    const HAVE_MAIL                     = 'have_mail';
    const INVALID_MAIL                  = 'invalid_mail';
    const DONT_HAVE_MAIL                = 'dont_have_mail';
    const MAIL_MESSAGE                  = 'mail_message';
    const UPDATE_PROFILE_ERROR          = 'update_profile_error';
    const UPDATE_PROFILE_SUCCESS        = 'update_profile_success';
    const PASSWORD_SEND_MAIL            = 'pwd_send_mail';
    const PASSWORD_INVALID              = 'pwd_invalid';
    const PASSWORD_SUCCESS              = 'pwd_success';
    const GENERIC_ERROR                 = 'generic_error';

    /**
     * @var false|mixed|void
     */
    private $options;


    public function __construct()
    {
        $this->options = get_option(self::OPTIONS_NAME);
        add_action( 'admin_init', array( $this, 'init_settings' ) );
    }

    public function init_settings()
    {
        register_setting(self::OPTIONS_GROUP, self::OPTIONS_NAME);
        add_settings_section( 'generic_section', 'Mensagens de Erro Genérica', null,self::OPTIONS_NAME );
        add_settings_field(
            self::GENERIC_ERROR,
            'Texto da mensagem',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'generic_section',
            [
                'name'  => self::GENERIC_ERROR
            ]
        );
        add_settings_section( 'user_profile_section', 'Mensagens de Perfil do Usuário', null,self::OPTIONS_NAME );
        add_settings_field(
            self::REGISTER_ERROR,
            'Erro cadastro novo usuário ',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::REGISTER_ERROR
            ]
        );
        add_settings_field(
            self::HAVE_MAIL,
            'E-mail já cadastrado',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::HAVE_MAIL
            ]
        );
        add_settings_field(
            self::DONT_HAVE_MAIL,
            'E-mail não cadastrado',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::DONT_HAVE_MAIL
            ]
        );
        add_settings_field(
            self::INVALID_MAIL,
            'E-mail inválido',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::INVALID_MAIL
            ]
        );
        add_settings_field(
            self::UPDATE_PROFILE_ERROR,
            'Erro ao atualizar perfil',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::UPDATE_PROFILE_ERROR
            ]
        );
        add_settings_field(
            self::UPDATE_PROFILE_SUCCESS,
            'Sucesso ao atualizar perfil',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::UPDATE_PROFILE_SUCCESS
            ]
        );
        add_settings_field(
            self::PASSWORD_INVALID,
            'Senha inválida',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::PASSWORD_INVALID
            ]
        );
        add_settings_field(
            self::PASSWORD_SUCCESS,
            'Senha alterada com sucesso',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::PASSWORD_SUCCESS
            ]
        );
        add_settings_field(
            self::PASSWORD_SEND_MAIL,
            'Recuperação de senha: envio e-mail',
            [ $this, 'input_text_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
            [
                'name'  => self::PASSWORD_SEND_MAIL
            ]
        );
        add_settings_field(
            self::MAIL_MESSAGE,
            'Recuperação: Mensagem de e-mail',
            [ $this, 'textarea_cb'],
            self::OPTIONS_NAME,
            'user_profile_section',
        );

    }

    public function input_text_cb( $args )
    {
        ?>
            <input class="regular-text" type="text" name="<?= self::OPTIONS_NAME ?>[<?= $args['name'] ?>]" id="<?= $args['name'] ?>"
                   value="<?= $this->options[$args['name']] ?? ''  ?>">
        <?php
    }

    public function textarea_cb()
    {
        ?>
            <textarea id='plugin_textarea_string' name='<?= self::OPTIONS_NAME ?>[<?= self::MAIL_MESSAGE ?>]' rows='7' cols='50' type='textarea'>
                <?= $this->options[self::MAIL_MESSAGE] ?? ''  ?>
            </textarea>
        <?php
    }


}