<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Entity;

use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;
use Zend\Form\Annotation;

/**
 * An example of how to implement a role aware user entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="users_grocery")
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class UserGrocery implements UserInterface, ProviderInterface
{
    const STATUS_DISABLED = false;
    const STATUS_ENABLED  = true;
    const SEC_COUNTER     = 0;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true,  length=255)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    /**
     * @var int
     * @ORM\Column(type="boolean")
     */
    protected $state = self::STATUS_ENABLED;

    /**
     * @ORM\ManyToOne(targetEntity="SiteUser\Entity\UserState")
     * @ORM\JoinColumn(name="user_state", referencedColumnName="id", nullable=true)
     * @Annotation\Type("DoctrineModule\Form\Element\ObjectSelect")
     * @Annotation\Options({
     * "empty_option": "",
     * "property": "stateName",
     * "label": "user state",
     * "label_attributes": {"class": "col-md-2 control-label"}
     * })
     */
    protected $userState;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $securityCounter = self::SEC_COUNTER;

    /**
     * @ORM\Column(name="last_login_datetime", type="datetime", nullable=true)
     * @Annotation\Exclude()
     */
    protected $lastLoginDateTime;


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="SiteUser\Entity\Role")
     * @ORM\JoinTable(name="user_grocery_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get displayName.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set displayName.
     *
     * @param string $displayName
     *
     * @return void
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get role.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles->getValues();
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    public function addRoles(Collection $roles)
    {
        foreach($roles as $role){
            $this->roles->add($role);
        }

    }

    public function removeRoles(Collection $roles) {
        foreach($roles as $role){
            $this->roles->removeElement($role);
        }
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }
 /**
     * @return the $userState
     */
    public function getUserState()
    {
        return $this->userState;
    }

    public function getSecurityCounter()
    {
        if ($this->securityCounter === null){
            $this->setSecurityCounter(0);
            return $this->securityCounter;
        }
        return $this->securityCounter;
    }

    /**
     * @return the $lastLoginDateTime
     */
    public function getLastLoginDateTime()
    {
        return is_object($this->lastLoginDateTime) ? clone $this->lastLoginDateTime : $this->lastLoginDateTime;
    }

 /**
     * @param field_type $userState
     */
    public function setUserState($userState)
    {
        $this->userState = $userState;
    }

 /**
     * @param number $securityCounter
     */
    public function setSecurityCounter($securityCounter)
    {
        $this->securityCounter = $securityCounter;
    }

    public function resetSecurityCounter($securityCounter = 0)
    {
        $this->securityCounter = $securityCounter;
    }

    public function addSecurityCounter()
    {
        $currentSecCounter = $this->securityCounter;
        if ($currentSecCounter === null){
            $currentSecCounter = 0;
        }
        $newSecCounter = $currentSecCounter + 1;
        $this->securityCounter = $newSecCounter;
    }

 /**
     * @param field_type $lastLoginDateTime
     */
    public function setLastLoginDateTime(\DateTime $lastLoginDateTime)
    {
        $this->lastLoginDateTime = $lastLoginDateTime;
    }


}
