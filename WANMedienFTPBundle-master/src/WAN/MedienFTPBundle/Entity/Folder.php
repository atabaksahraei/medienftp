<?php

namespace WAN\MedienFTPBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity(repositoryClass="WAN\MedienFTPBundle\Entity\FolderRepository")
*/
class Folder
{
	/**
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;
	
	/**
	* @ORM\Column(type="string", length=255)
	*/
	protected $name;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Folder")
	 * @ORM\JoinColumn(name="parent_folder_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $parentFolder;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $size;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $mostCurrentDate;

    /**
     * @ORM\OneToMany(targetEntity="FolderZip", mappedBy="folder")
     **/
    protected $zips;

    /**
     * @ORM\OneToMany(targetEntity="FolderView", mappedBy="folder")
     **/
    protected $views;

    public function __construct() {
        $this->views = new ArrayCollection();
        $this->zips = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Folder
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set mostCurrentDate
     *
     * @param \DateTime $mostCurrentDate
     * @return Folder
     */
    public function setMostCurrentDate($mostCurrentDate)
    {
        $this->mostCurrentDate = $mostCurrentDate;

        return $this;
    }

    /**
     * Get mostCurrentDate
     *
     * @return \DateTime 
     */
    public function getMostCurrentDate()
    {
        return $this->mostCurrentDate;
    }

    /**
     * Set parentFolder
     *
     * @param \WAN\MedienFTPBundle\Entity\Folder $parentFolder
     * @return Folder
     */
    public function setParentFolder(\WAN\MedienFTPBundle\Entity\Folder $parentFolder = null)
    {
        $this->parentFolder = $parentFolder;

        return $this;
    }

    /**
     * Get parentFolder
     *
     * @return \WAN\MedienFTPBundle\Entity\Folder 
     */
    public function getParentFolder()
    {
        return $this->parentFolder;
    }

    /**
     * Add zips
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderZip $zips
     * @return Folder
     */
    public function addZip(\WAN\MedienFTPBundle\Entity\FolderZip $zips)
    {
        $this->zips[] = $zips;

        return $this;
    }

    /**
     * Remove zips
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderZip $zips
     */
    public function removeZip(\WAN\MedienFTPBundle\Entity\FolderZip $zips)
    {
        $this->zips->removeElement($zips);
    }

    /**
     * Get zips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getZips()
    {
        return $this->zips;
    }

    /**
     * Add views
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderView $views
     * @return Folder
     */
    public function addView(\WAN\MedienFTPBundle\Entity\FolderView $views)
    {
        $this->views[] = $views;

        return $this;
    }

    /**
     * Remove views
     *
     * @param \WAN\MedienFTPBundle\Entity\FolderView $views
     */
    public function removeView(\WAN\MedienFTPBundle\Entity\FolderView $views)
    {
        $this->views->removeElement($views);
    }

    /**
     * Get views
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViews()
    {
        return $this->views;
    }
}
