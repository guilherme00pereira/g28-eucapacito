<?php

namespace G28\Eucapacito\Models;

use G28\Eucapacito\Core\OptionsManager;

class User
{

    private $id;
    private $email;
    private $name;
    private $password;
    private $birthdate;
    private $phone;
    private $country;
    private $state;
    private $city;


    public function __construct()
    {
        $this->options = get_option(OptionsManager::OPTIONS_NAME);
    }

    public function setUserByEmail( $mail ): User
    {
        $user           = get_user_by( 'email', $mail);
        $this->id       = $user->ID;
        $this->email    = $mail;
        return $this;
    }

    public function createWPUser(): array
    {
        if (is_email($this->email)) {
            if (!email_exists($this->email)) {
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
                if (is_wp_error($newUserId)) {
                    return [false, $this->options[OptionsManager::REGISTER_ERROR]];
                }
                return [true, [
                    'id'            => $newUserId,
                    'username'      => $username,
                    'first_name'    => $name[0],
                    'last_name'     => $name[1]
                ]];
            } else {
                return [false, $this->options[OptionsManager::HAVE_MAIL]];
            }
        } else {
            return [false, $this->options[OptionsManager::INVALID_MAIL]];
        }
    }

    public function updateWPUser(): array
    {
        $name  = explode(' ', $this->name, 2);
        $user  = wp_update_user([
            'ID'                    => $this->id,
            'user_email'            => $this->email,
            'first_name'            => $name[0],
            'last_name'             => $name[1],
        ]);
        update_user_meta( $this->id,'data_de_nascimento', $this->birthdate);
        update_user_meta( $this->id,'telefone', $this->phone);
        update_user_meta( $this->id,'pais', $this->country);
        update_user_meta( $this->id,'estado', $this->state);
        update_user_meta( $this->id,'cidade', $this->city);

        if (is_wp_error($user)) {
            return [false, $this->options[OptionsManager::UPDATE_PROFILE_ERROR]];
        }
        return [true, $this->options[OptionsManager::UPDATE_PROFILE_SUCCESS]];
    }

    public function generateNewPassword(): string
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        $pwd = substr(str_shuffle($data), 0, 8);
        wp_set_password( $pwd, $this->id );
        return $pwd;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of birthdate
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set the value of birthdate
     *
     * @return  self
     */
    public function setBirthdate($day, $month, $year)
    {
        $this->birthdate = $year . $month . $day;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */
    public function setPhone($ddd, $number)
    {
        $this->phone = $ddd . $number;

        return $this;
    }

    /**
     * Get the value of country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the value of country
     *
     * @return  self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the value of state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the value of state
     *
     * @return  self
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get the value of city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return  self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
