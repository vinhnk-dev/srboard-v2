<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

class BoardCategoryRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\BoardCategory::class;
    }

    public function getBaseUrl(){}
    public function getSearchFields(){}
    public function rules(){}

    public function getCategory()
    {
        return $this->model->get();
    }

    public function deleteCate($id)
    {
        DB::table('boards')
            ->where('board_category_id', "=", $id)
            ->delete();

        $category = $this->model->select('board_categories')
            ->where('id', $id)
            ->delete();
        
        return $category;
    }

    public function updateCate($id, $validated)
    {
        return DB::table('board_categories')
            ->where('id', $id)
            ->update($validated);
    }
}