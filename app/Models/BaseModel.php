<?php

namespace App\Models;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    
    protected $repo = null;
    protected $formatCell = [];
    protected $tableHeader = [];

    public function __construct()
    {
        
    }

    public function getCellValue($field){
        if(isset($this->formatCell[$field])){
            $this->repo = new ('\\App\\Repositories\\'.(new \ReflectionClass($this))->getShortName() . 'Repository')();
            $temp = $this->formatCell[$field];
            return $temp($this);
        }
        return $this->$field;
    }

    public function getTableHeader(){
        return $this->tableHeader;
    }

    public function setRepo($repo){
        $this->repo = $repo;
    }

    public function repo(){
        return new ('\\App\\Repositories\\'.(new \ReflectionClass($this))->getShortName() . 'Repository')();
    }
}
