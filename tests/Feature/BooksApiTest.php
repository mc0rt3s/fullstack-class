<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;

class BooksApiTest extends TestCase{

    use RefreshDatabase;

    /** @test */

    function can_get_all_books(){

        $books = Book::factory(10)->create();

        $this->getJson(route('books.index'))->assertJsonFragment([
            'title' => $books[0]->title
        ]);
    }

    /** @test */

    function can_get_one_book(){
        $book = Book::factory()->create(); // Primero crea el libre para poder correr el test
        $this->getJson(route('books.show', $book))->assertJsonFragment([
            'title' => $book->title
        ]);
    }

    /** @test */
    function can_create_books(){

        $this->postJson(route('books.store'),[])->assertJsonValidationErrorFor('title');

        $this->postJson(route('books.store'),[
            'title' => 'My Test New Book'
        ])->assertJsonFragment([
            'title' => 'My Test New Book'
        ]);

        $this->assertDatabaseHas('books',[
            'title' => 'My Test New Book'
        ]);
    }

    /** @test */
    function can_update_books(){

        $book = Book::factory()->create(); // Primero debe crear el libro para poder validar si se puede modificar.

        $this->patchJson(route('books.update', $book),[])->assertJsonValidationErrorFor('title');

        $this->patchJson(route('books.update', $book), [
            'title' => 'Libro updated!'
        ])->assertJsonFragment([
            'title' => 'Libro updated!'
        ]);

        $this->assertDatabaseHas('books',[ // Aca como primer parametro se le entrega el nombre de la tabla en la base de datos.
            'title' => 'Libro Updated!'
        ]);
    }


    /** @test */
    function can_delete_books(){
        $book = Book::factory()->create(); // Primero debe crear el libro para poder validar si se puede modificar.

        $this->deleteJson(route('books.destroy', $book))->assertNoContent();

        $this->assertDatabaseCount('books',0);
    }
}
