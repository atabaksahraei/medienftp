<?php

namespace WAN\MedienFTPBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array('currentAsLink' => false));
        // $menu->addChild('sync', array('route' => 'wan_medien_ftp_sync'));

        // is logged in?
        if ($this->container->get('security.context')->isGranted('ROLE_USER') === true) {

            $menu->addChild('files', array('route' => 'wan_medien_ftp_folder_list'));
            $menu->addChild('profile', array('route' => 'fos_user_profile_show'));

            // is admin?
            if ($this->container->get('security.context')->isGranted('ROLE_ADMIN') === true) {
                $menu->addChild('group', array('route' => 'fos_user_group_list'));
            }
            $menu->addChild('logout', array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild('login', array('route' => 'wan_medien_ftp_login'));
        }

        return $menu;
    }
}