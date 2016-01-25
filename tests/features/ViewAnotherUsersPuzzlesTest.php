<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Puzzle;
use App\Models\User;

class ViewAnotherUsersPuzzlesTest extends TestCase
{
    use DatabaseMigrations;
        
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testViewingAnotherUsersPuzzles()
    {   
        $user = factory(User::class)->create(['username' => 'johndoe']);
        $puzzle = factory(Puzzle::class)->make(['name' => 'My first puzzle']);
        
        $user->puzzles()->save($puzzle);
        
        $this->visit('/johndoe')->see('My first puzzle');
    }
}
