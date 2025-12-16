<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;

class BoardFileRepository extends BaseRepository
{
    public function getModel()
    {
        return \App\Models\BoardFile::class;
    }

    public function getBaseUrl(){}
    public function getSearchFields(){}
    public function rules(){}

    public function getBoardFile($id)
    {
        $board_files = $this->model->select('board_files.*')
            ->where("board_id", "=", $id)
            ->get();

        return $board_files;
    }

    public function deleteBoardFile($id)
    {   
        $board_files = $this->getBoardFile($id);

        foreach($board_files as $file){
            $publicPath = public_path();
            $filePath = $publicPath . '/' . $file->file_url;
            if (file_exists($filePath)) {
                if(unlink($filePath)){
                    $file->delete();
                }
            }
        }
    }

    public function saveFile($upload_file, $path)
    {
        $get_name_file = $upload_file->getClientOriginalName();
        $name_file = pathinfo($get_name_file, PATHINFO_FILENAME);
        $extension = $upload_file->getClientOriginalExtension();
        $new_name = $name_file . "." . $extension;

        if (file_exists($path . $new_name)) {
            $count = 0;
            while (file_exists($path . $new_name)) {
                $count++;
                $new_name = $name_file . "(" . $count . ")." . $extension;
            }
        }
        $upload_file->move($path, $new_name);
        return $new_name;
    }
}