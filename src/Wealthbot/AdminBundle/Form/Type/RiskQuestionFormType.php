<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 28.11.12
 * Time: 17:59
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;

class RiskQuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'textarea', array(
                'attr' => array('rows' => 3)
            ))
            ->add('answers', 'collection', array(
                'type' => new RiskAnswerFormType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
        ;

        $builder->addEventListener(FormEvents::BIND, function(FormEvent $event) {
            /** @var \Wealthbot\RiaBundle\Entity\RiskQuestion $data */
            $data = $event->getData();
            $form = $event->getForm();

            $answersCount = $data->getAnswers()->count();

            if ($answersCount < 2) {
                $form->get('title')->addError(new FormError('The question should have at least %nb_answers% answers', array('%nb_answers%' => 2)));
            } elseif ($answersCount > 5) {
                $form->get('title')->addError(new FormError('The question should have no more than %nb_answers% answers', array('%nb_answers%' => 5)));
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wealthbot\RiaBundle\Entity\RiskQuestion'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'rx_risk_question';
    }

}
