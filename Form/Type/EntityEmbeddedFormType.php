<?php
namespace Oxygen\FrameworkBundle\Form\Type;

use Oxygen\FrameworkBundle\Form\Transformer\EntityToFormModelTransformer;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * Formulaire d'édition d'un ticket d'accès à un évènement
 * 
 * @author lolozere
 *
 */
abstract class EntityEmbeddedFormType extends AbstractType {
	
	protected $dataClass;
	protected $entityClass;
	
	public function __construct($dataClass, $entityClass) {
		$this->dataClass = $dataClass;
		$this->entityClass = $entityClass;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		// Transform
		$transformer = new EntityToFormModelTransformer($this->dataClass, $this->entityClass);
		$builder->addModelTransformer($transformer);
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		 $resolver->setDefaults(array(
		 		'data_class' => $this->dataClass,
		 		'cascade_validation' => true,
		 ));
	}
	
}