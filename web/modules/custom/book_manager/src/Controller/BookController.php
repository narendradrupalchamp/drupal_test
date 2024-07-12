<?php

namespace Drupal\book_manager\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;


class BookController extends ControllerBase {

    protected $entityTypeManager;

    public function __construct(EntityTypeManagerInterface $entityTypeManager) {
        $this->entityTypeManager = $entityTypeManager;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    public function listBooks() {
        // Fetch and return list of books
         // Load all Book nodes.
         $book_storage = $this->entityTypeManager()->getStorage('node');
         $books = $book_storage->loadByProperties(['type' => 'book']);
 
         // Define table headers.
         $header = [
             $this->t('Title'),
             $this->t('Author'),
             $this->t('Publication Year'),
             $this->t('Operations'),
         ];
 
         $rows = [];
         foreach ($books as $book) {

             $rows[] = [
                 'title' => $book->getTitle(), //getting node title
                 'author' => $book->get('field_author')->value, //getting field value
                 'publication_year' => $book->get('field_publication_year')->value,
                 'operations' => [
                     'data' => [
                        '#markup' => $this->t('<a href="@edit-url">Edit</a> | <a href="@delete-url">Delete</a>', [
                            '@edit-url' => Url::fromRoute('book_manager.edit_book_form', ['node' => $book->id()])->toString(),
                            '@delete-url' => Url::fromRoute('book_manager.delete_book_modal', ['node' => $book->id()])->toString(),
                        ]),
                    ],
                 ],
             ];
         }
 
         // Build the table render array.
         $build = [
             '#theme' => 'table',
             '#header' => $header,
             '#rows' => $rows,
             '#attributes' => ['class' => ['book-table']],
         ];
 
         return $build;
    }
    
    //to edit book node edit function
    public function editBook(Node $node) {
        return $this->formBuilder()->getForm(BookEditForm::class, $node);
    }

    //delete book controller function
    public function deleteBookModal(Node $node) {
       return $this->formBuilder()->getForm('Drupal\book_manager\Form\BookDeleteModalForm', $node);
    }
}
