<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\Logger;
use G28\Eucapacito\Models\User;
use G28\Eucapacito\Options\MessageOptions;
use G28\Eucapacito\Core\Plugin;
use WP_REST_Response;

class UserEndpoints
{

    /**
     * @var false|mixed|null
     */
    private $options;

    public function __construct()
    {
        $this->options = get_option(MessageOptions::OPTIONS_NAME);
    }

    public function registerUser( $request ): WP_REST_Response
    {
        try {
            $user = new User();
            $user->setEmail($request['email'])
                ->setName($request['name'])
                ->setPassword($request['password']);
            list($created, $response) = $user->createWPUser();
            if ($created) {
                return new WP_REST_Response($response, 200);
            }
            return new WP_REST_Response($response, 500);
        } catch (\Exception $e) {
            Logger::getInstance()->add("registerUser", "Erro ao registrar usuário: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function loginOrRegisterSocialUser( $request ): WP_REST_Response
    {
        try {
            $mail = $request['email'];
            if ( email_exists( $mail ) ) {
                $user = get_user_by_email( $mail );
                if( !is_bool( $user ) ) {
                    return new WP_REST_Response([
                        'id'            => $user->ID,
                        'email'         => $mail,
                        'username'      => $user->user_login,
                        'first_name'    => $user->first_name,
                        'last_name'     => $user->last_name,
                    ], 200);
                }
            } else {
                $user = new User();
                $user->setEmail( $mail )
                    ->setName( $request['name'] )
                    ->setPassword( $request['password'] );
                list($created, $response) = $user->createWPUser();
                if ($created) {
                    return new WP_REST_Response($response, 200);
                }
                return new WP_REST_Response($response, 500);
            }
        } catch (\Exception $e) {
            Logger::getInstance()->add("loginOrRegisterSocialUser", "Erro ao logar/registrar usuário Google: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function updateUser( $request ): WP_REST_Response
    {
        try {
            $user = new User();
            $user->setId( $request['id'])
                ->setEmail( $request['email'] )
                ->setName( $request['full_name'] )
                ->setBirthdate( $request['b_day'], $request['b_month'], $request['b_year'] )
                ->setPhone( $request['phone_ddd'], $request['phone_number'] )
                ->setCountry( $request['country'] )
                ->setState( $request['state'] )
                ->setCity( $request['city'] );
            list($created, $response) = $user->updateWPUser();
            if( $created ) {
                return new WP_REST_Response( $response, 200 );
            }
            return new WP_REST_Response( $response , 500 );
        } catch (\Exception $e) {
            Logger::getInstance()->add("updateUser", "Erro ao atualizar usuário: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function recoverPassword( $request ): WP_REST_Response
    {
        $mail = $request['email'];
        try {
            if ( is_email( $mail ) ) {
                if ( !email_exists( $mail ) ) {
                    return new WP_REST_Response($this->options[MessageOptions::DONT_HAVE_MAIL], 500);
                } else {
                    ob_start();
                    include sprintf( "%smail/recover-password.php", Plugin::getTemplateDir() );
                    $html = ob_get_clean();
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    wp_mail(
                        $mail,
                        "Eu Capacito - Recuperação de senha",
                       $html,
                        $headers
                    );
                    return new WP_REST_Response($this->options[MessageOptions::PASSWORD_SEND_MAIL], 200);
                }
            } else {
                return new WP_REST_Response($this->options[MessageOptions::INVALID_MAIL], 500);
            }
        } catch (\Exception $e) {
            Logger::getInstance()->add("recoverPassword", "Erro ao recuperar senha: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function changePassword( $request ): WP_REST_Response
    {
        try {
            $user_id = $request['id'];
            $user = get_user_by('id', $user_id);
            $old = $request['oldPassword'];
            $new = $request['newPassword'];
            $hash = $user->data->user_pass;
            if (wp_check_password($old, $hash)) {
                wp_set_password($new, $user_id);
                return new WP_REST_Response($this->options[MessageOptions::PASSWORD_SUCCESS], 200);
            } else {
                return new WP_REST_Response($this->options[MessageOptions::PASSWORD_INVALID], 500);
            }
        } catch (\Exception $e) {
            Logger::getInstance()->add("changePassword", "Erro ao trocar senha: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function resetPassword( $request ): WP_REST_Response
    {
        try {
            $code       = $request['code'];
            if(empty($code)) {
                return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
            } else {
                Logger::getInstance()->add("resetPassword", "code: - " . $code);
                $user_id = get_transient($code);
                Logger::getInstance()->add("resetPassword", "user: - " . $user_id);
                $new = $request['password'];
                Logger::getInstance()->add("resetPassword", "pwd: - " . $new);
                wp_set_password($new, $user_id);
                return new WP_REST_Response($this->options[MessageOptions::PASSWORD_SUCCESS], 200);
            }
        } catch (\Exception $e) {
            Logger::getInstance()->add("resetPassword", "Erro ao redefinitr senha: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function verifyResetLink( $request ): WP_REST_Response
    {
        try {
            if( is_bool( get_transient( $request['code'] ) ) ) {
                return new WP_REST_Response(false, 200);
            }
            return new WP_REST_Response(true, 410);
        } catch (\Exception $e) {
            Logger::getInstance()->add("changePassword", "Erro ao redefinir senha: - " . $e->getMessage());
            return new WP_REST_Response($this->options[MessageOptions::GENERIC_ERROR], 500);
        }
    }

    public function avatar( $request ): WP_REST_Response
    {
        $userId     = $request['user_id'];
        $mediaId    = $request['media_id'];
        $user       = new User();
        $img        = $user->saveAvatar( $userId, $mediaId );
        return new WP_REST_Response([ 'image' => $img ], 200);
    }

}
