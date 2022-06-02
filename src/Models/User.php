<?php

namespace G28\Eucapacito\Models;

class User {

    private $email;
    private $name;
    private $password;

    public function __construct( $email, $name, $pwd )
    {
        $this->email    = $email;
        $this->name     = $name;
        $this->password = $pwd;

    }

    public function createWPUser(): array
    {
        if( is_email($this->email) ) {
           if( !email_exists( $this->email ) ) {
               $name       = explode(' ', $this->name, 2);
               $username   = strstr($this->email, '@', true);
               $newUserId  = wp_insert_user([
                   'user_pass'      => $this->password,
                   'user_login'     => $username,
                   'user_nicename'  => $username,
                   'user_email'     => $this->email,
                   'first_name'     => $name[0],
                   'last_name'      => $name[1],
                   'role'           => 'subscriber'
               ]);
               if( is_wp_error( $newUserId ) ){
                   return [ false, 'Erro ao cadastrar.' ];
               }
               return [ true, [
                   'id'            => $newUserId,
                   'username'      => $username,
                   'first_name'    => $name[0],
                   'last_name'     => $name[1]
               ] ];
           } else {
               return [false, 'e-mail já cadastrado'];
            }
        } else {
            return [false, 'e-mail inválido'];
        }
    }

    public function recoverUserPassword( $email )
    {
        $user = get_user_by( 'email', $email );

        wp_insert_user([
            
        ]);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

}