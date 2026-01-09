<?php
namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Get statuses: ['pending' => 'Pending', 'confirmed' => 'Confirmed', ...]
        $statuses = Order::getAvailableStatuses();

        // Flip to get ['Pending' => 'pending', 'Confirmed' => 'confirmed', ...]
        // This is the correct format for ChoiceType (label => value)
        $choices = array_flip($statuses);

        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Order Status',
                'choices' => $choices,
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}

