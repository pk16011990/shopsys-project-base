<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Model\FileUpload\FileUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;

class FileUploadType extends AbstractType implements DataTransformerInterface {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \Symfony\Component\Validator\Constraint[]
	 */
	private $constraints;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'file_upload';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'error_bubbling' => false,
			'compound' => true,
			'file_constraints' => array(),
		));
	}

	/**
	 * @param array $value
	 * @return string
	 */
	public function reverseTransform($value) {
		return $value['file_uploaded'];
	}

	/**
	 * @param string $value
	 * @return array
	 */
	public function transform($value) {
		return array(
			'file_uploaded' => $value,
			'file' => null,
		);
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);
		$this->constraints = $options['file_constraints'];

		$builder->addModelTransformer($this);
		$builder
			->add('file_uploaded', 'hidden', array(
				'constraints' => array(
					new Constraints\Callback(array($this, 'validateUploadedFile')),
				),
			))
			->add('file', 'file');

		// fallback for IE9 and older
		$builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
	}

	/**
	 * @param string $filenameUploaded
	 * @param \Symfony\Component\Validator\ExecutionContextInterface $context
	 */
	public function validateUploadedFile($filenameUploaded, ExecutionContextInterface $context) {
		$filepath = $this->fileUpload->getCacheFilepath($filenameUploaded);
		$file = new File($filepath);
		$context->validateValue($file, $this->constraints);
	}

	/**
	 * @param \Symfony\Component\Form\FormEvent $event
	 */
	public function onPreSubmit(FormEvent $event) {
		$data = $event->getData();
		if ($data['file'] instanceof UploadedFile) {
			try {
				$cachedFilename = $this->fileUpload->upload($data['file']);
				$this->fileUpload->tryDeleteCachedFile($data['file_uploaded']);
				$data['file'] = null;
				$data['file_uploaded'] = $cachedFilename;
				$event->setData($data);
			} catch (\SS6\ShopBundle\Model\FileUpload\Exception\FileUploadException $ex) {
				$event->getForm()->addError('Nahrání souboru se nezdařilo.');
			}
		}
	}

}