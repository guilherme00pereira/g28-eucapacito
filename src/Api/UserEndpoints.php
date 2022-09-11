<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\Logger;
use G28\Eucapacito\Models\User;
use G28\Eucapacito\Options\MessageOptions;
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
        try {
            if (is_email($request['email'])) {
                if (!email_exists($request['email'])) {
                    return new WP_REST_Response($this->options[MessageOptions::DONT_HAVE_MAIL], 500);
                } else {
                    $user = new User();
                    $newPwd = $user->setUserByEmail($request['email'])->generateNewPassword();
                    $message = get_option(MessageOptions::OPTIONS_NAME)[MessageOptions::MAIL_MESSAGE];
                    wp_mail(
                        $request['email'],
                        "Eu Capacito - Recuperação de senha",
                        $message . " " . $newPwd
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

    public function avatar( $request ): WP_REST_Response
    {
        $userId     = $request['user_id'];
        $mediaId    = $request['media_id'];
        update_post_meta( $mediaId, 'is_avatar', true );
        update_user_meta( $userId, 'avatar_id', $mediaId);
        $img        = wp_get_attachment_image_url( $mediaId );
        return new WP_REST_Response([ 'image' => $img ], 200);
    }

}