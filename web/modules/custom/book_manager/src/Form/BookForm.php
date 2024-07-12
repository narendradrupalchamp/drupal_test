<?php

namespace Drupal\book_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class BookForm extends FormBase {

    public function getFormId() {
        return 'book_manager_add_book_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state, Node $node = null) {
        // Form elements here (title, author, publication_year, etc.)
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Book Title'),
            '#required' => TRUE,
        ];

        $form['author'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Author'),
            '#required' => TRUE,
        ];

        $form['publication_year'] = [
            '#type' => 'number',
            '#title' => $this->t('Publication Year'),
            '#required' => TRUE,
            '#min' => 1000,
            '#max' => date('Y'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Add Book'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        //validate publication year
        $year = $form_state->getValue('publication_year');
        if ($year < 2000 || $year > date('Y')) {
            $form_state->setErrorByName('publication_year', $this->t('Please enter a valid publication year.'));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Save node logic here
        $node = Node::create([
            'type' => 'book',
            'title' => $form_state->getValue('title'),
            'field_author' => $form_state->getValue('author'),
            'field_publication_year' => $form_state->getValue('publication_year'),
            'status' => 1, // Set status to published.
        ]);
        $node->save();

        $this->messenger()->addMessage($this->t('The book has been added.'));
        $form_state->setRedirect('book_manager.list_books'); // Redirect to the list page.
    }
}
