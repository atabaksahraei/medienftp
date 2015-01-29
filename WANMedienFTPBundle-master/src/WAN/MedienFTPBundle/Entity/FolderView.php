<?php

namespace WAN\MedienFTPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FolderView
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="folderViews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="views")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $folder;

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
     * Set time
     *
     * @param \DateTime $time
     * @return FolderView
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set user
     *
     * @param \WAN\MedienFTPBundle\Entity\User $user
     * @return FolderView
     */
    public function setUser(\WAN\MedienFTPBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \WAN\MedienFTPBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set folder
     *
     * @param \WAN\MedienFTPBundle\Entity\Folder $folder
     * @return FolderView
     */
    public function setFolder(\WAN\MedienFTPBundle\Entity\Folder $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return \WAN\MedienFTPBundle\Entity\Folder 
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
