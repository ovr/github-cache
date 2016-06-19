<?php

namespace App\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *      db="github",
 *      collection="users"
 * )
 */
class User
{
    /**
     * @ODM\Id(strategy="NONE", type="int")
     */
    protected $id;

    /**
     * @ODM\Field(nullable=false)
     * @ODM\UniqueIndex
     */
    protected $login;

    /** @ODM\Field(type="boolean", value="false") */
    protected $site_admin;

    /**
     * @ODM\Field()
     * @var string
     */
    protected $name;

    /**
     * @ODM\Field()
     * @var string
     */
    protected $company;

    /**
     * @ODM\Field()
     * @var string
     */
    protected $blog;

    /**
     * @ODM\Field()
     * @var string
     */
    protected $location;

    /**
     * @ODM\Field()
     * @var string
     */
    protected $email;

    /**
     * @ODM\Field()
     * @var string
     */
    protected $bio;

    /**
     * @ODM\Field(type="boolean")
     * @var boolean
     */
    protected $hireable = false;

    /**
     * @ODM\Field(type="integer")
     * @var integer
     */
    protected $public_repos;

    /**
     * @ODM\Field(type="integer")
     * @var integer
     */
    protected $followers;

    /**
     * @ODM\Field(type="integer")
     * @var integer
     */
    protected $following;

    /**
     * @ODM\Date()
     */
    protected $created_at;

    /**
     * @ODM\Date()
     */
    protected $updated_at;

    /**
     * @return mixed
     */
    public function getSiteAdmin()
    {
        return $this->site_admin;
    }

    /**
     * @param mixed $site_admin
     */
    public function setSiteAdmin($site_admin)
    {
        $this->site_admin = $site_admin;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return int
     */
    public function getPublicRepos()
    {
        return $this->public_repos;
    }

    /**
     * @param int $public_repos
     */
    public function setPublicRepos($public_repos)
    {
        $this->public_repos = $public_repos;
    }

    /**
     * @return boolean
     */
    public function isHireable()
    {
        return $this->hireable;
    }

    /**
     * @param boolean $hireable
     */
    public function setHireable($hireable)
    {
        $this->hireable = $hireable;
    }

    /**
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param string $bio
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @param string $blog
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return int
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param int $following
     */
    public function setFollowing($following)
    {
        $this->following = $following;
    }

    /**
     * @return int
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param int $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    }
}
