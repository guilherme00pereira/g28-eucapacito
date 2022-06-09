<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Core\Logger;
use G28\Eucapacito\Core\OptionsManager;
use G28\Eucapacito\Models\User;
use WP_REST_Response;

class UserEndpoints
{

    public function __construct()
    {
        $this->options = get_option(OptionsManager::OPTIONS_NAME);
    }

    public function registerUser( $request ): WP_REST_Response
    {
        $user = new User();
        $user->setEmail($request['email'])
            ->setName($request['name'])
            ->setPassword($request['password']);
        list($created, $response) = $user->createWPUser();
        if( $created ) {
            return new WP_REST_Response( $response, 200 );
        }
        return new WP_REST_Response( $response , 500 );
    }

    public function updateUser( $request ): WP_REST_Response
    {
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
    }

    public function recoverPassword( $request ): WP_REST_Response
    {
        if (is_email($request['email'])) {
            if (!email_exists($request['email'])) {
                return new WP_REST_Response($this->options[OptionsManager::DONT_HAVE_MAIL], 500);
            } else {
                $user       = new User();
                $newPwd     = $user->setUserByEmail( $request['email'] )->generateNewPassword();
                wp_mail(
                    $request['email'],
                    "Eu Capacito - Recuperação de senha",
                    "Sua nova senha: ${newPwd}"
                );
                return new WP_REST_Response( $this->options[OptionsManager::PASSWORD_SEND_MAIL] , 200 );
            }
        } else {
            return new WP_REST_Response($this->options[OptionsManager::INVALID_MAIL], 500);
        }
    }

    public function changePassword( $request ): WP_REST_Response
    {
        $user_id = $request['id'];
        $user = get_user_by( 'id', $user_id );
        $old = $request['oldPassword'];
        $new = $request['newPassword'];
        $hash = $user->data->user_pass;
        if( wp_check_password( $old, $hash ) ){
            wp_set_password( $new, $user_id );
            return new WP_REST_Response( $this->options[OptionsManager::PASSWORD_SUCCESS] , 200 );
        }else {
            return new WP_REST_Response($this->options[OptionsManager::PASSWORD_INVALID], 500);
        }
    }

    public function avatar( $request ): WP_REST_Response
    {
        
        var_dump($request['file'][0]);
        return new WP_REST_Response('ok', 200);
        
    }

}