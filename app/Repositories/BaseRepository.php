<?php

namespace App\Repositories;

use App\Repositories\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;
    protected $searchFields = [];
    protected $indexPath = "index";

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function rules();
    abstract public function getModel();
    abstract public function getBaseUrl();
    abstract public function getSearchFields();

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
        $this->setSearchFields();
    }

    public function setSearchFields()
    {
        $this->searchFields = $this->getSearchFields();
    }
    public function setBaseUrl()
    {
        $this->indexPath = $this->getBaseUrl();
    }

    public function getClassName()
    {
        return (new \ReflectionClass(new $this->model()))->getShortName();
    }

    public function emptyModal()
    {
        return new $this->model();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        $result = $this->model->find($id);
        return $result;
    }

    public function search($trash = false, $query = null, $rowlimit = true)
    {
        $limit = request()->has('perpage') ? request()->get('perpage') : 15;
        $offset = request()->has('page') ? (request()->get('page') - 1) * $limit : 0;
        $key = trim(request()->get('search'));
        $start_date_search = request()->has('start_date_search') ? request()->get('start_date_search') : "";
        $end_date_search = request()->has('end_date_search') ? request()->get('end_date_search') : "";

        $data_query = $this->model->select($this->model->getTable() . ".*");

        $where = [];
        foreach ($this->searchFields as $field) {
            $where[] = [$field, "like", "%$key%"];
        }
        if (count($where) > 0 && $key != '') $data_query->orWhere($where);

        $timeWhere = [];
        if ($start_date_search != "" && $end_date_search != "") {
            $timeWhere[] = [$this->model->getTable() . '.created_at', '>=', date('Y-m-d H:i:s', strtotime($start_date_search . " 00:00:00"))];
            $timeWhere[] = [$this->model->getTable() . '.created_at', '<=', date('Y-m-d H:i:s', strtotime($end_date_search . " 23:59:59"))];
        }
        if (count($timeWhere) > 0) $data_query = $data_query->where($timeWhere);

        if ($query != null) $query($data_query);

        if ($trash) $count = $data_query->whereNotNull($this->model->getTable() . '.deleted_at')->get()->count();
        else $count = $data_query->whereNull($this->model->getTable() . '.deleted_at')->get()->count();

        if($rowlimit) $data_query = $data_query->offset($offset)->limit($limit);
        if ($trash) $list = $data_query->whereNotNull($this->model->getTable() . '.deleted_at')->orderBy($this->model->getTable() . '.id', 'desc')->get();
        else $list = $data_query->whereNull($this->model->getTable() . '.deleted_at')->orderBy($this->model->getTable() . '.id', 'desc')->get();

        return ['count'=>$count, 'list'=>$list];
    }

    public function get()
    {
        return $this->model->get();
    }

    public function count()
    {
        return $this->model->count();
    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if ($result) {
            $result = $result->update($attributes);
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        // dd($id);
        try{
            $result = $this->find($id);
            if ($result) {
                // dd($result);
                $result->delete($id);
                return true;
            }
        }catch(\Exception){
            return false;
        }

    }

    public function restore($id)
    {
        $record = $this->model->whereNotNull('deleted_at')->find($id);
        if ($record) {
            $record->restore();
            return true;
        }
        return false;
    }

    public function forceDelete($id)
    {
        try{
            $record = $this->model->find($id);
            if ($record) {
                $record->forceDelete();
                return true;
            }
        } catch(\Exception) {
            return false;
        }
    }

    public function toStringList($list, $element = "element", $default = "No items", $symbol = ","){
        $s = "";
        foreach ($list as $v) $s .= $v->$element . $symbol." ";
        $s = substr_replace($s, "", -2);
        return $s == "" ? $default : $s;
    }
}
