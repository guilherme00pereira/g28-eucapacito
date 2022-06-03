<?php

namespace G28\Eucapacito\Models;

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
                    return [false, 'Erro ao cadastrar.'];
                }
                return [true, [
                    'id'            => $newUserId,
                    'username'      => $username,
                    'first_name'    => $name[0],
                    'last_name'     => $name[1]
                ]];
            } else {
                return [false, 'e-mail já cadastrado'];
            }
        } else {
            return [false, 'e-mail inválido'];
        }
    }

    public function updateWPUser(): array
    {
        $name       = explode(' ', $this->name, 2);
        $user  = wp_update_user([
            'user_email'            => $this->email,
            'first_name'            => $name[0],
            'last_name'             => $name[1],
            'data_de_nascimento'    => $this->birthdate,
            'telefone'              => $this->phone,
            'pais'                  => $this->country,
            'estado'                => $this->state,
            'cidade'                => $this->city
        ]);
        if (is_wp_error($user)) {
            return [false, 'Erro ao atualizar.'];
        }
        return [true, ''];
    }

    public function recoverUserPassword($email)
    {
        $user = get_user_by('email', $email);

        wp_insert_user([]);
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
