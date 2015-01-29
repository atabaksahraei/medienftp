<?php

namespace WAN\MedienFTPBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FileDownload
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fileDownloads")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="File", inversedBy="downloads")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $file;

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
     * @return FileDownload
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
     * @return FileDownload
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
     * Set file
     *
     * @param \WAN\MedienFTPBundle\Entity\File $file
     * @return FileDownload
     */
    public function setFile(\WAN\MedienFTPBundle\Entity\File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \WAN\MedienFTPBundle\Entity\File 
     */
    public function getFile()
    {
        return $this->file;
    }
}
