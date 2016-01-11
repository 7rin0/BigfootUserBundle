<?php

namespace Bigfoot\Bundle\UserBundle\Entity;

use Bigfoot\Bundle\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="bigfoot_user")
 * @ORM\Entity(repositoryClass="Bigfoot\Bundle\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\ManyToMany(targetEntity="Bigfoot\Bundle\UserBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="bigfoot_user_role")
     */
    protected $roles;

    /**
     * Construct User
     */
    public function __construct()
    {
        parent::__construct();

        $this->roles = new ArrayCollection();

        $this->setEnabled(true);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set full name
     *
     * @param string $fullName
     * @return User
     */
    public function setFullname($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullName;
    }

    /**
     * Add Role.
     *
     * @return BigfootUser
     */
    public function addRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function getRoles()
    {
        foreach ($this->roles as $role) {
            $roles[] = $role->getName();
        }

        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function getFormRoles()
    {
        return $this->roles;
    }

    /**
     * Remove a User Role.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles
     * @return BigfootUser
     */
    public function removeRole($role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->salt,
                $this->password,
                $this->locale,
            )
        );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
            $this->locale,
        ) = unserialize($serialized);
    }
}
