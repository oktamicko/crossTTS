<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuzzleTemplate extends Model {
	protected $table = 'puzzle_templates';
	public $timestamps = FALSE;
    
    protected $rules = array(
        'name'      => 'required',
        'width'     => 'required|numeric|min:5|max:20',
        'height'    => 'required|numeric|min:5|max:20',
        'user_id'   => 'required',
    );
    
    private $errors;
    
    public function validate($data){
        $v = \Validator::make($data, $this->rules);
        
        if ($v->fails()){
            $this->errors = $v->errors();
            return false;
        }

        return true;
    }

    public function errors(){
        return $this->errors;
    }
    
    public function puzzleTemplateSquares(){
        return $this->hasMany(PuzzleTemplateSquare::class);
    }
    
    public static function create(array $args = array()){
        $v = new PuzzleTemplate;
        if ($v->validate($args)){
            $pt = new PuzzleTemplate;
            $pt->name = $args['name'];
            $pt->width = $args['width'];
            $pt->height = $args['height'];
            $pt->user_id = $args['user_id'];
            $pt->timestamp_utc = time();
            $pt->slug = PuzzleTemplate::findSlug($args['name']);
            $pt->save();
            
            for($col = 1; $col <= $pt->width; $col++){
                for($row = 1; $row <= $pt->height; $row++){
                    $sq = new PuzzleTemplateSquare;
                    $sq->puzzle_template_id = $pt->id;
                    $sq->row = $row;
                    $sq->col = $col;
                    if (in_array($row.'-'.$col, $args['blackSquares'])){
                        $sq->square_type = 'black';
                    }else{
                        $sq->square_type = 'white';
                    }
                    $sq->save();
                }
            }
            
            return $pt;
        }else{
            return array('errors' => $v->errors);
        }
    }
    
    public static function findSlug($name){
        $slug = preg_replace("[^a-zA-Z\d]", "-", $name);
        $origslug = $slug;
        $exists = PuzzleTemplate::where('slug', $slug)->first();
        $i = 0;
        while ($exists){
            $slug = $origslug."-".$i++;
            $exists = PuzzleTemplate::where('slug', $slug)->first();
        }
        return $slug;
    }
}