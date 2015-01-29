<?php

namespace WAN\MedienFTPBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
use WAN\MedienFTPBundle\Entity\Group as Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="SET NULL")
     * 
     */
	protected $group;

    /**
     * @ORM\OneToMany(targetEntity="FileDownload", mappedBy="user")
     **/
    protected $fileDownloads;

    /**
     * @ORM\OneToMany(targetEntity="FileZip", mappedBy="user")
     **/
    protected $fileZips;

    /**
     * @ORM\OneToMany(targetEntity="FolderView", mappedBy="user")
     **/
    protected $folderViews;

    /**
     * @ORM\OneToMany(targetEntity="FolderZip", mappedBy="user")
     **/
    protected $folderZips;

    public function __construct() {
        parent::__construct();
        $this->fileDownloads = new ArrayCollection();
        $this->fileZips = new ArrayCollection();
        $this->folderViews = new ArrayCollection();
        $this->folderZips = new ArrayCollection();
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
     * Set group
     *
     * @param \WAN\MedienFTPBundle\Entity\Group $group
     * @return User
     */
    public function setGroup(\WAN\MedienFTPBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \WAN\MedienFTPBundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add fileDownloads
     *
     * @param \WAN\MedienFTPBundle\Entity\FileDownload $fileDownloads
     * @return User
     */
    public function addFileDownload(\WAN\MedienFTPBundle\Entity\FileDownload $fileDownloads)
    {
        $this->fileDownloads[] = $fileDownloads;

        return $this;
    }

    /**
     * Remove fileDownloads
     *
     * @param \WAN\MedienFTPBundle\Entity\FileDownload $fileDownloads
     */
    public function removeFileDownload(\WAN\MedienFTPBundle\Entity\FileDownload $fileDownloads)
    {
        $this->fileDownloads->removeElement($fileDownloads);
    }

    /**
     * Get fileDownloads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFileDownloads()
    {
        return $this->fileDownloads;
    }

    /**
     * Add fileZips
     *
     * @param \WAN\MedienFTPBundle\Entity\FileZip $fileZips
     * @return User
     */
    public function addFileZip(\WAN\MedienFTPBundle\Entity\FileZip $fileZips)
    {
        $this->fileZips[] = $fileZips;

        return $this;
    }

    /**
     * Remove fileZips
     *
     * @param \WAN\MedienFTPBundle\Entity\FileZip $fileZips
     */
    public function removeFileZip(\WAN\MedienFTPBundle\Entity\FileZip $fileZips)
    {
        $this->fileZips->removeElement($fileZips);
    }

    /**
     * Get fileZips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFileZips()
    {
        return $this->fileZips;
    }

    /**
     * Add folderViews
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderView $folderViews
     * @return User
     */
    public function addFolderView(\WAN\MedienFTPBundle\Entity\FolderView $folderViews)
    {
        $this->folderViews[] = $folderViews;

        return $this;
    }

    /**
     * Remove folderViews
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderView $folderViews
     */
    public function removeFolderView(\WAN\MedienFTPBundle\Entity\FolderView $folderViews)
    {
        $this->folderViews->removeElement($folderViews);
    }

    /**
     * Get folderViews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFolderViews()
    {
        return $this->folderViews;
    }

    /**
     * Add folderZips
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderZip $folderZips
     * @return User
     */
    public function addFolderZip(\WAN\MedienFTPBundle\Entity\FolderZip $folderZips)
    {
        $this->folderZips[] = $folderZips;

        return $this;
    }

    /**
     * Remove folderZips
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderZip $folderZips
     */
    public function removeFolderZip(\WAN\MedienFTPBundle\Entity\FolderZip $folderZips)
    {
        $this->folderZips->removeElement($folderZips);
    }

    /**
     * Get folderZips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFolderZips()
    {
        return $this->folderZips;
    }
}
