<?php

namespace WAN\MedienFTPBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
* @ORM\Entity(repositoryClass="WAN\MedienFTPBundle\Entity\FileRepository")
*/
class File
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
     * @ORM\OneToMany(targetEntity="FileZip", mappedBy="file")
     **/
    protected $zips;

    /**
     * @ORM\OneToMany(targetEntity="FileDownload", mappedBy="file")
     **/
    protected $downloads;

    public function __construct() {
        $this->downloads = new ArrayCollection();
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
     * @return File
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
     * @return File
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
     * @return File
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
     * @return File
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
     * @param \WAN\MedienFTPBundle\Entity\FileZip $zips
     * @return File
     */
    public function addZip(\WAN\MedienFTPBundle\Entity\FileZip $zips)
    {
        $this->zips[] = $zips;

        return $this;
    }

    /**
     * Remove zips
     *
     * @param \WAN\MedienFTPBundle\Entity\FileZip $zips
     */
    public function removeZip(\WAN\MedienFTPBundle\Entity\FileZip $zips)
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
     * Add downloads
     *
     * @param \WAN\MedienFTPBundle\Entity\FileDownload $downloads
     * @return File
     */
    public function addDownload(\WAN\MedienFTPBundle\Entity\FileDownload $downloads)
    {
        $this->downloads[] = $downloads;

        return $this;
    }

    /**
     * Remove downloads
     *
     * @param \WAN\MedienFTPBundle\Entity\FileDownload $downloads
     */
    public function removeDownload(\WAN\MedienFTPBundle\Entity\FileDownload $downloads)
    {
        $this->downloads->removeElement($downloads);
    }

    /**
     * Get downloads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }
}
