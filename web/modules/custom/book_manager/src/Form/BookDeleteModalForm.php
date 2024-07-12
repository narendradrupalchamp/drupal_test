<?php

namespace Drupal\book_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Url;


class BookDeleteModalForm extends FormBase {

    protected $node;

    public function getFormId() {
        return 'book_manager_delete_book_modal_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, Node $node = NULL) {
        
        $this->node = $node;

        $form['confirm'] = [
            '#type' => 'markup',
            '#markup' => $this->t('Are you sure you want to delete the book "@title"?', ['@title' => $this->node->getTitle()]),
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['actions']['delete'] = [
            '#type' => 'submit',
            '#value' => $this->t('Delete'),
            '#button_type' => 'danger',
        ];

        // Redirect to the custom page on cancel.
        $form['actions']['cancel'] = [
            '#type' => 'link',
            '#title' => $this->t('Cancel'),
            '#url' => \Drupal\Core\Url::fromRoute('book_manager.list_books'),
            '#attributes' => ['class' => ['button']],
        ]; 

        return $form;
    }

    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        try {
            $this->node->delete();
            $this->messenger()->addMessage($this->t('The book has been deleted.'));
            $form_state->setRedirect('book_manager.list_books'); // Redirect to the list page after deletion.
        } catch (EntityStorageException $e) {
            $this->messenger()->addError($this->t('An error occurred while deleting the book.'));
        }
    }
}
