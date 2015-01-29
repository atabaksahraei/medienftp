<?php

namespace WAN\MedienFTPBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Response;
use WAN\MedienFTPBundle\Entity\File;
use WAN\MedienFTPBundle\Entity\FileDownload;
use WAN\MedienFTPBundle\Entity\FileZip;
use WAN\MedienFTPBundle\Entity\Folder;
use WAN\MedienFTPBundle\Entity\FolderView;
use WAN\MedienFTPBundle\Entity\FolderZip;

class MediaController extends Controller
{
    public function syncAction()
    {
        $filesFolder = $this->container->getParameter("absolute_files_path");
        $folderRepo = $this->getDoctrine()->getRepository("WANMedienFTPBundle:Folder");
        $fileRepo = $this->getDoctrine()->getRepository("WANMedienFTPBundle:File");
        $em = $this->getDoctrine()->getManager();

        //////////////////////////////////////////////////////////////////
        // check for deleted files / folders
        // and check for updated meta data (most current date and size)
        //////////////////////////////////////////////////////////////////

        $updatedFolders = array();
        $updatedFiles = array();

        $deletedFolders = array();
        $deletedFiles = array();

        $allFolders = $folderRepo->findAll();
        $allFiles = $fileRepo->findAll();

        foreach ($allFiles as $file) {
            $realFile = new \SplFileInfo($filesFolder . "/" . $file->getName());
            if ($realFile->isFile()) {
                // update date and size
                $newMostCurrentDate = new \DateTime();
                $newMostCurrentDate->setTimestamp(max($realFile->getCTime(), $realFile->getMTime()));

                if ($realFile->getSize() != $file->getSize() || $newMostCurrentDate != $file->getMostCurrentDate()) {
                    $file->setSize($realFile->getSize());
                    $file->setMostCurrentDate($newMostCurrentDate);
                    $updatedFiles[] = $file;
                }
            } else {
                $em->remove($file);
                $deletedFiles[] = $file;
            }
        }
        $em->flush();

        foreach ($allFolders as $folder) {
            $realFolder = new \SplFileInfo($filesFolder . "/" . $folder->getName());
            if ($realFolder->isDir()) {
                // update date and size
                $size = 0; // size of folder = size of files in it
                $mostCurrentTimestamp = 0; // date of folder = date of newest file

                $subFinder = new Finder();
                $subFinder->in($realFolder->getRealPath());
                foreach ($subFinder->files() as $subFile) {
                    $size += $subFile->getSize();
                    $mostCurrentTimestamp = max($mostCurrentTimestamp, $subFile->getCTime(), $subFile->getMTime());
                }

                $mostCurrentDate = new \DateTime();
                $mostCurrentDate->setTimestamp($mostCurrentTimestamp);

                if ($folder->getSize() != $size || $folder->getMostCurrentDate() != $mostCurrentDate) {
                    $folder->setMostCurrentDate($mostCurrentDate);
                    $folder->setSize($size);
                    $updatedFolders[] = $folder;
                }
            } else {
                $em->remove($folder);
                $deletedFolders[] = $folder;
            }
        }
        $em->flush();


        ///////////////////////////////////////
        // check for new files / folders
        ///////////////////////////////////////

        $newFiles = array();
        $newFolders = array();

        $finder = new Finder();
        $finder->in($filesFolder);

        foreach ($finder as $media) {
            $path = $media->getRelativePathname();
            $path = str_replace("\\", "/", $path);

            if ($media->isFile()) {
                $dbFile = $fileRepo->findOneByName($path);

                if ($dbFile == null) // if $media is not in db
                {
                    $newFile = new File();
                    $newFile->setName($path);
                    $newFile->setSize($media->getSize());

                    $mostCurrentDate = new \DateTime();
                    $mostCurrentDate->setTimestamp(
                        max($media->getCTime(), $media->getMTime())
                    ); // find newest date in creation time and modify time
                    $newFile->setMostCurrentDate($mostCurrentDate);

                    if (strpos($path, "/") !== false) // if this file has parent directories
                    {
                        $parentFolderName = substr($path, 0, strrpos($path, "/"));
                        $parentFolder = $folderRepo->findOneByName($parentFolderName);

                        $newFile->setParentFolder($parentFolder);
                    }

                    $em->persist($newFile);
                    $newFiles[] = $newFile;
                }
            } else {
                if ($media->isDir()) {
                    $dbFolder = $folderRepo->findOneByName($path);

                    if ($dbFolder == null) // if $media is not in db
                    {
                        $path = str_replace("\\", "/", $path);

                        $newFolder = new Folder();
                        $newFolder->setName($path);

                        $size = 0; // size of folder = size of files in it
                        $mostCurrentTimestamp = 0; // date of folder = date of newest file

                        $subFinder = new Finder();
                        $subFinder->in($media->getRealPath());
                        foreach ($subFinder->files() as $subFile) {
                            $size += $subFile->getSize();
                            $mostCurrentTimestamp = max(
                                $mostCurrentTimestamp,
                                $subFile->getCTime(),
                                $subFile->getMTime()
                            );
                        }

                        $newFolder->setSize($size);

                        $mostCurrentDate = new \DateTime();
                        $mostCurrentDate->setTimestamp($mostCurrentTimestamp);
                        $newFolder->setMostCurrentDate($mostCurrentDate);

                        if (strpos($path, "/") !== false) // if this folder has parent directories
                        {
                            $parentFolderName = substr($path, 0, strrpos($path, "/"));
                            $parentFolder = $folderRepo->findOneByName($parentFolderName);
                            $newFolder->setParentFolder($parentFolder);
                        }

                        $em->persist($newFolder);
                        $em->flush(); // needed, because parent folder can only be found, if it is in db
                        $newFolders[] = $newFolder;
                    }
                }
            }
        }
        $em->flush();

        return $this->render(
            "WANMedienFTPBundle:Media:sync.html.twig",
            array(
                "newFiles" => $newFiles,
                "newFolders" => $newFolders,
                "updatedFiles" => $updatedFiles,
                "updatedFolders" => $updatedFolders,
                "deletedFiles" => $deletedFiles,
                "deletedFolders" => $deletedFolders
            )
        );
    }

    public function dirListAction($id)
    {
        $folderRepo = $this->getDoctrine()->getRepository("WANMedienFTPBundle:Folder");
        $fileRepo = $this->getDoctrine()->getRepository("WANMedienFTPBundle:File");
        $viewArray = array();

        if ($id == 0) {
            $folders = $folderRepo->findRootFolders();
            $files = $fileRepo->findRootFiles();
        } else {
            $parentFolder = $folderRepo->findOneById($id);
            if (!$parentFolder) {
                throw $this->createNotFoundException("Folder not found");
            }

            // log folder view
            $folderView = new FolderView();
            $folderView->setUser($this->get('security.context')->getToken()->getUser());
            $folderView->setFolder($parentFolder);
            $folderView->setTime(new \DateTime());
            // updating folder object in database
            $em = $this->getDoctrine()->getManager();
            $em->persist($folderView);
            $em->flush();

            $folders = $folderRepo->findByParentFolder($parentFolder);
            $files = $fileRepo->findByParentFolder($parentFolder);

            $viewArray["parentFolder"] = $parentFolder;

            $viewArray["breadcrumbs"] = array($parentFolder);

            $tempParent = $parentFolder;
            while ($tempParent->getParentFolder() !== null) {
                $tempParent = $tempParent->getParentFolder();
                $viewArray["breadcrumbs"][] = $tempParent;
            }
            $viewArray["breadcrumbs"] = array_reverse($viewArray["breadcrumbs"]);
        }

        $viewArray["folders"] = $folders;
        $viewArray["files"] = $files;

        return $this->render("WANMedienFTPBundle:Media:dirList.html.twig", $viewArray);
    }

    public function dirZipAction($id)
    {
        $folderRepo = $this->getDoctrine()->getRepository("WANMedienFTPBundle:Folder");
        $zipDir = $folderRepo->findOneById($id);

        if (!$zipDir) {
            throw $this->createNotFoundException("Folder not found");
        }

        // log folder zip
        $folderZip = new FolderZip();
        $folderZip->setUser($this->get('security.context')->getToken()->getUser());
        $folderZip->setFolder($zipDir);
        $folderZip->setTime(new \DateTime());
        // updating file object in database
        $em = $this->getDoctrine()->getManager();
        $em->persist($folderZip);
        $em->flush();

        $filesFolder = $this->container->getParameter("absolute_files_path");
        $tempZipFolder = $this->container->getParameter("absolute_temp_zip_path");

        $splZipDir = new SplFileInfo($filesFolder . "/" . $zipDir->getName(
        ), "", ""); // use Symfony SplFileInfo, because of getContents() but ignore relative paths "", ""

        $zipArch = new \ZipArchive();

        $tempZipFile = new SplFileInfo($tempZipFolder . "/" . rand(
            100000,
            999999
        ) . ".zip", "", ""); // use Symfony SplFileInfo, because of getContents() but ignore relative paths "", ""

        if ($zipArch->open($tempZipFile->getPathname(), \ZIPARCHIVE::CREATE) !== true) {
            throw new \Exception("ZIP archive could not be created");
        }

        $finder = new Finder();
        $finder->in($splZipDir->getRealPath());
        $zipIsEmpty = true;

        foreach ($finder->files() as $file) {
            $zipIsEmpty = false;
            $zipArch->addFile($file->getRealPath(), iconv("UTF-8", "CP850//IGNORE", $file->getRelativePathname()));
        }
        $zipArch->close();

        if ($zipIsEmpty) {
            throw new \Exception("ZIP archive is empty");
        }
        $contents = $tempZipFile->getContents();

        $headers = array(
            "Content-type" => "application/zip",
            "Content-Disposition" => 'attachment; filename="' . $splZipDir->getBasename() . '.zip"',
            "Content-Encoding" => ": plainbinary",
            "Content-Transfer-Encoding" => "binary",
            "Content-Length" => $tempZipFile->getSize()
        );

        // delete temp zip file
        unlink($tempZipFile->getRealPath());

        return new Response($contents, 200, $headers);
    }

    public function fileZipAction($id)
    {
        $fileRepo = $this->getDoctrine()->getRepository("WANMedienFTPBundle:File");
        $zipFile = $fileRepo->findOneById($id);

        if (!$zipFile) {
            throw $this->createNotFoundException("File not found");
        }

        // log file zip
        $fileZip = new FileZip();
        $fileZip->setUser($this->get('security.context')->getToken()->getUser());
        $fileZip->setFile($zipFile);
        $fileZip->setTime(new \DateTime());
        // updating file object in database
        $em = $this->getDoctrine()->getManager();
        $em->persist($fileZip);
        $em->flush();

        $filesFolder = $this->container->getParameter("absolute_files_path");
        $tempZipFolder = $this->container->getParameter("absolute_temp_zip_path");

        $splZipFile = new SplFileInfo($filesFolder . "/" . $zipFile->getName(
        ), "", ""); // use Symfony SplFileInfo, because of getContents() | ignore relative paths "", ""

        $zipArch = new \ZipArchive();

        $tempZipFile = new SplFileInfo($tempZipFolder . "/" . rand(
            100000,
            999999
        ) . ".zip", "", ""); // use Symfony SplFileInfo, because of getContents() | ignore relative paths "", ""

        if ($zipArch->open($tempZipFile->getPathname(), \ZIPARCHIVE::CREATE) !== true) {
            throw new \Exception("ZIP archive could not be created");
        }

        $zipArch->addFile($splZipFile->getRealPath(), iconv("UTF-8", "CP850//IGNORE", $splZipFile->getFilename()));
        $zipArch->close();

        $contents = $tempZipFile->getContents();

        $headers = array(
            "Content-type" => "application/zip",
            "Content-Disposition" => 'attachment; filename="' . $splZipFile->getBasename() . '.zip"',
            "Content-Encoding" => ": plainbinary",
            "Content-Transfer-Encoding" => "binary",
            "Content-Length" => $tempZipFile->getSize()
        );

        unlink($tempZipFile->getRealPath());

        return new Response($contents, 200, $headers);
    }

    public function fileDownloadAction($id)
    {
        $filesFolder = $this->container->getParameter("absolute_files_path");
        $dbFile = $this->getDoctrine()->getRepository("WANMedienFTPBundle:File")->findOneById($id);

        if (!$dbFile) {
            throw $this->createNotFoundException("File not found");
        }

        $splFile = new SplFileInfo($filesFolder . "/" . $dbFile->getName(
        ), "", ""); // use Symfony SplFileInfo, because of getContents() | ignore relative paths "", ""

        if ($splFile->isFile()) {
            // log file download
            $fileDownload = new FileDownload();
            $fileDownload->setUser($this->get('security.context')->getToken()->getUser());
            $fileDownload->setFile($dbFile);
            $fileDownload->setTime(new \DateTime());
            // updating file object in database
            $em = $this->getDoctrine()->getManager();
            $em->persist($fileDownload);
            $em->flush();

            $headers = array(
                "Content-Type" => "application/octet-stream",
                "Content-Disposition" => 'attachment; filename="' . $splFile->getBasename() . '"',
                "Content-Encoding" => "plainbinary",
                "Content-Transfer-Encoding" => "binary",
                "Content-Length" => $splFile->getSize()
            );

            return new Response($splFile->getContents(), 200, $headers);
        }

        throw $this->createNotFoundException();
    }
}
