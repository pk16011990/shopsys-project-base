<?php

namespace SS6\ShopBundle\Form\Admin\Login;

use SS6\ShopBundle\Model\Administrator\Administrator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class LoginFormType extends AbstractType {

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím login'))
				)
			))
			->add('password', 'password', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím heslo'))
				)
			))
			->add('login', 'submit');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'admin_login';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => Administrator::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
