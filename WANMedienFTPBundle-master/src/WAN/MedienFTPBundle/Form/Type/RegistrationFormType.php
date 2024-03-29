<?php

namespace WAN\MedienFTPBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
   //      parent::buildForm($builder, $options);

//          $this->setWidgets(array(
//          		'name'    => new sfWidgetFormInputText(),
//          		'email'   => new sfWidgetFormInputText(),
//          		'subject' => new sfWidgetFormSelect(array('choices' => self::$subjects)),
//          		'message' => new sfWidgetFormTextarea(),
//          ));

          
           $builder
           ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
           ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
           ->add('plainPassword', 'repeated', array(
           		'type' => 'password',
           		'options' => array('translation_domain' => 'FOSUserBundle'),
           		'first_options' => array('label' => 'form.password'),
           		'second_options' => array('label' => 'form.password_confirmation'),
           		'invalid_message' => 'fos_user.password.mismatch',))
               ->add('group', null, array(
                   'empty_value' => 'Wähle eine Gruppe',
                   'label' => 'form.group_name',
                   'translation_domain' => 'FOSUserBundle',))
           ;
          


    }

    public function getName()
    {
        return 'wan_user_registration';
    }
}