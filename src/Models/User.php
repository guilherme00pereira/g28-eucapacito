<?php

namespace G28\Eucapacito\Models;

use Exception;
use G28\Eucapacito\Core\Logger;
use G28\Eucapacito\Options\MessageOptions;

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
    /**
     * @var false|mixed|null
     */
    private $options;


    public function __construct()
    {
        $this->options = get_option(MessageOptions::OPTIONS_NAME);
    }

    /**
     * @throws Exception
     */
    public function createWPUser(): array
    {
        if (is_email($this->email)) {
            if (!email_exists($this->email)) {
                $name           = explode(' ', $this->name, 2);
                $preUsername    = strstr($this->email, '@', true);
                $username       = $this->generateUsername( $preUsername );
                $newUserId      = wp_insert_user([
                    'user_pass'      => $this->password,
                    'user_login'     => $username,
                    'user_nicename'  => $username,
                    'user_email'     => $this->email,
                    'first_name'     => $name[0],
                    'last_name'      => $name[1],
                    'role'           => 'subscriber'
                ]);
                if (is_wp_error($newUserId)) {
                    throw new Exception("usuário: " . $username . " - messagem - " . $newUserId->get_error_message());
                }
                return [true, [
                    'id'            => $newUserId,
                    'username'      => $username,
                    'first_name'    => $name[0],
                    'last_name'     => $name[1],
                    'password'      => $this->password
                ]];
            } else {
                return [false, $this->options[MessageOptions::HAVE_MAIL]];
            }
        } else {
            return [false, $this->options[MessageOptions::INVALID_MAIL]];
        }
    }

    /**
     * @throws Exception
     */
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
            throw new Exception("usuário: " . $name[0] . " " . $name[1] . " - messagem - " . $user->get_error_message());
        }
        return [true, $this->options[MessageOptions::UPDATE_PROFILE_SUCCESS]];
    }

    public function saveAvatar( $userId, $mediaId)
    {
        update_post_meta( $mediaId, 'is_avatar', true );
        update_user_meta( $userId, 'avatar_id', $mediaId);
        return wp_get_attachment_image_url( $mediaId );
    }

    private function generateUsername( $username ): string
    {
        if(username_exists( $username ) )
        {
            return $username . "_" . crc32($username . strval( time() ));
        } else {
            return $username;
        }
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
     * @param $email
     * @return  self
     */
    public function setEmail($email): User
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
     * @param $name
     * @return  self
     */
    public function setName($name): User
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
     * @param $password
     * @return  self
     */
    public function setPassword($password): User
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
     * @param $day
     * @param $month
     * @param $year
     * @return  self
     */
    public function setBirthdate($day, $month, $year): User
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
     * @param $ddd
     * @param $number
     * @return  self
     */
    public function setPhone($ddd, $number): User
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
     * @param $country
     * @return  self
     */
    public function setCountry($country): User
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
     * @param $state
     * @return  self
     */
    public function setState($state): User
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
     * @param $city
     * @return  self
     */
    public function setCity($city): User
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
     * @param $id
     * @return  self
     */ 
    public function setId($id): User
    {
        $this->id = $id;

        return $this;
    }
}
