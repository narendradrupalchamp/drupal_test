<?php

namespace Drupal\book_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityStorageException;

class BookEditForm extends FormBase {

    protected $node;

    public function getFormId() {
        return 'book_manager_edit_book_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, Node $node = NULL) {
        // Load the existing node data.
        if ($node) {
            $this->node = $node;
        } 
        
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Book Title'),
            '#required' => TRUE,
            '#default_value' => $this->node->getTitle(),
        ];

        $form['author'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Author'),
            '#required' => TRUE,
            '#default_value' => $this->node->get('field_author')->value,
        ];

        $form['publication_year'] = [
            '#type' => 'number',
            '#title' => $this->t('Publication Year'),
            '#required' => TRUE,
            '#min' => 1000,
            '#max' => date('Y'),
            '#default_value' => $this->node->get('field_publication_year')->value,
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Update Book'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        $year = $form_state->getValue('publication_year');
        if ($year < 1000 || $year > date('Y')) {
            $form_state->setErrorByName('publication_year', $this->t('Please enter a valid publication year.'));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Update the node fields.
        $this->node->setTitle($form_state->getValue('title'));
        $this->node->set('field_author', $form_state->getValue('author'));
        $this->node->set('field_publication_year', $form_state->getValue('publication_year'));

        try {
            $this->node->save();
            $this->messenger()->addMessage($this->t('The book has been updated.'));
            $form_state->setRedirect('book_manager.list_books'); // Redirect to the list page after submission.
        } catch (EntityStorageException $e) {
            $this->messenger()->addError($this->t('An error occurred while updating the book.'));
        }
    }
}
