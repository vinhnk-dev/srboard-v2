<?php

namespace App\View\Components;

class TableView
{
    public static function render_trash($repo, $context, $configs = [])
    {
        $default = [];
        $context['pageName'] = $repo->getClassName();
        $context['tableHead'] = $repo->emptyModal()->getTableHeader();
        $context['baseUrl'] = $repo->getBaseUrl();
        $parentid = request()->parentid ?? 0;
        $url_param = $parentid != 0 ? ['parentid' => $parentid] : [];

        //Table tools
        $configs['tools'] = [
            'list' => [
                "content" => '<em class="icon ni ni-list-fill"></em>  <span>Back to List</span>',
                "class" => "btn-primary",
                "href" => "",
                "attrs" => ["title" => "Back to List", 'url' => route($context['baseUrl'] . '.index', $url_param)]
            ]
        ];
        $context['display_tools'] = true;
        $context['tableview_tool'] =  TableView::render_action($configs['tools'], $configs['tools']);

        //Paging
        if (isset($configs['paging'])) {
            $paging = $configs['paging'];
        } else {
            $paging = true;
        }
        $context['display_paging'] = $paging;
        if ($paging) {
            $perpage = request()->has('perpage') ? request()->get('perpage') : 15;
            $context['perpage'] = $perpage;

            $page = request()->has('page') ? request()->get('page') : 1;
            $search = request()->get('search');
            $baseUrl = route($repo->getBaseUrl() . '.trash', $url_param) . '?page=:page&perpage=' . $perpage . '&search=' . $search;

            $search_result =  $repo->search(true);
            $context['list'] = $search_result['list'];
            $context['tableview_paging'] = $paging ?
                TableView::render_paging($baseUrl, $search_result['count'], $page, $perpage) : "";
        }
        if (!isset($context['list'])) $context['list'] = $repo->search()['list'];

        //Row action
        if (isset($configs['actions'])) {
            $context['display_action'] = count($configs['actions']) > 0;
        } else {
            $configs['actions'] = TableView::default_trash_config($context['baseUrl'], $url_param);
            $context['display_action'] = true;
        }
        if ($context['display_action']) {
            $default['actions'] = TableView::default_trash_config($context['baseUrl'], $url_param);
            foreach ($context['list'] as $modal) {
                $modal->row_actions = TableView::render_action($configs['actions'], $default['actions'], $modal->id);
            }
        }

        //Table filter
        $default['filters'] = TableView::default_filter_config();
        if (isset($configs['filters'])) {
            $context['display_filters'] = count($configs['filters']) > 0;
        } else {
            $configs['filters'] = $default['filters'];
            $context['display_filters'] = true;
        }
        $context['tableview_filter'] = TableView::render_filter($configs['filters']);
        return $context;
    }

    public static function render_normal($repo, $context, $configs = [])
    {
        $default = [];
        $context['pageName'] = $repo->getClassName();
        $context['tableHead'] = $repo->emptyModal()->getTableHeader();
        $context['baseUrl'] = $repo->getBaseUrl();

        $parentid = request()->parentid ?? 0;
        $url_param = $parentid != 0 ? ['parentid' => $parentid] : [];

        //Table tools
        if (isset($configs['tools'])) {
            $context['display_tools'] = count($configs['tools']) > 0;
        } else {
            $configs['tools'] = TableView::default_tool_config($context['baseUrl'], $url_param);
            $context['display_tools'] = true;
        }
        if( $context['display_tools']){
            $default['tools'] = TableView::default_tool_config($context['baseUrl'], $url_param);
            $context['tableview_tool'] =  TableView::render_action($configs['tools'], $default['tools']);
        }

        //Paging
        if (isset($configs['paging'])) {
            $paging = $configs['paging'];
        } else {
            $paging = true;
        }
        $context['display_paging'] = $paging;
        if ($paging) {
            $perpage = request()->has('perpage') ? request()->get('perpage') : 15;
            $context['perpage'] = $perpage;

            $page = request()->has('page') ? request()->get('page') : 1;
            $search = request()->get('search');
            $baseUrl = route($repo->getBaseUrl() . '.index', $url_param) . '?page=:page&perpage=' . $perpage . '&search=' . $search;

            $search_result = $repo->search();
            $context['list'] = $search_result['list'];
            $context['totalRow'] = $search_result['count'];
            $context['tableview_paging'] = $paging ?
                TableView::render_paging($baseUrl, $search_result['count'], $page, $perpage) : "";
        }
        if (!isset($context['list'])){
            $search_result = $repo->search();
            $context['list'] = $search_result['list'];
            $context['totalRow'] = $search_result['count'];
        } 

        //Row action
        if (isset($configs['actions'])) {
            $context['display_action'] = count($configs['actions']) > 0;
        } else {
            $configs['actions'] = TableView::default_action_config($context['baseUrl'], $url_param);
            $context['display_action'] = true;
        }
        if ($context['display_action']) {
            $default['actions'] = TableView::default_action_config($context['baseUrl'], $url_param);
            foreach ($context['list'] as $modal) {
                $modal->row_actions = TableView::render_action($configs['actions'], $default['actions'], $modal->id);
            }
        }


        //Table filter
        $default['filters'] = TableView::default_filter_config();
        if (isset($configs['filters'])) {
            $context['display_filters'] = count($configs['filters']) > 0;
        } else {
            $configs['filters'] = $default['filters'];
            $context['display_filters'] = true;
        }
        $context['tableview_filter'] = TableView::render_filter($configs['filters']);

        return $context;
    }

    public static function render_filter($actions)
    {
        $filter = "";
        if (isset($actions['time'])) {
            $attr = 'autocomplete="off" type="text" ';
            if (isset($actions['time']['attrs']))
                foreach ($actions['time']['attrs'] as $k => $v) {
                    $attr .= $k . '="' . $v . '" ';
                }
            $class = isset($actions['time']['class']) ? $actions['time']['class'] : "";

            $filter .= '<div class="d-flex mt-1 mr-1">
                <input class="border input-image2 ip-period date-picker' . $class . ' " 
                placeholder="Start date" 
                name="start_date_search" ' . $attr . '  
                value="' . request()->get('start_date_search') . '">

                <span class="mx-1 tt-per-set pt-1">~</span>

                <input class="border input-image2 ip-period date-picker' . $class . ' " 
                placeholder="End date"  
                name="end_date_search" ' . $attr . '  
                value="' . request()->get('end_date_search') . '">
            </div>';
        }

        if (isset($actions['text'])) {
            $attr = "";
            if (isset($actions['text']['attrs']))
                foreach ($actions['text']['attrs'] as $k => $v) {
                    $attr .= $k . '="' . $v . '" ';
                }
            $class = isset($actions['text']['class']) ? $actions['text']['class'] : "";
            $filter .= '<div class="d-flex mt-1 ml-2"><input type="text" name="search" 
                            class="border rounded-top-left-10px rounded-bottom-left-10px px-2' . $class . ' " 
                            ' . $attr . ' 
                            value="' . request()->get('search') . '" style="margin-left:-12px">';
            $filter .= '<button type="submit" class="btn btn-search px-2">
            <em class="icon ni ni-search"></em></button></div>';
        }

        if (isset($actions['time']) && !isset($actions['text']))
            $filter .= '<button type="submit" 
            class="btn btn-search rounded-top-left-10px rounded-bottom-left-10px ml-2">
            <em class="icon ni ni-search"></em></button>';

        return $filter;
    }

    public static function render_action($actions, $default, $target_id = 0)
    {
        $act = '';
        foreach ($actions as $k => $v) {
            $content = isset($v['content']) ? $v['content'] : $default[$k]['content'];
            $class = isset($v['class']) ? $v['class'] : $default[$k]['class'];
            $href = isset($v['href']) ? $v['href'] : $default[$k]['href'];
            $attrs = isset($v['attrs']) ? $v['attrs'] : $default[$k]['attrs'];
            if ($target_id > 0) $act .= TableView::render_action_element($content, $class, $href, $attrs, $target_id);
            else $act .=  TableView::render_tool_element($content, $class, $href, $attrs);
        }
        return $act;
    }

    public static function render_tool_element($content = "", $class = "", $href = "", $attrs = [])
    {
        $attr = "";
        foreach ($attrs as $k => $v) {
            $attr .= $k . '="' . $v . '" ';
        }
        $href = trim($href);
        $href = $href != '' && $href != '#' ? ' href="' . $href . ' " ' : '';
        return '<a class="btn btn-export ' . $class . ' text-white p-1 mr-1 pr-2 mt-1" ' . $href . '  ' . $attr . ' data-toggle="tooltip" >
                    ' . $content . '
                </a>';
    }

    public static function render_action_element($content = "", $class = "", $href = "", $attrs = [], $target_id = "0")
    {
        $attr = "";
        foreach ($attrs as $k => $v) {
            $attr .= $k . '="' . $v . '" ';
        }
        $href = trim(str_replace('@@id@@', $target_id, $href));
        $href = $href != '' && $href != '#' ? ' href="' . $href . ' " ' : '';
        return '<a class="btn ' . $class . ' text-white p-1 ml-1" ' . $href . '  ' . str_replace('@@id@@', $target_id, $attr) . ' data-toggle="tooltip" >
                    ' . $content . '
                </a>';
    }

    public static function default_filter_config()
    {
        return [
            "text" => [
                'class' => "",
                "attrs" => ["placeholder" => "Search..."]
            ],
            "time" => [
                'class' => "",
                "attrs" => []
            ],
        ];
    }

    public static function default_tool_config($baseUrl, $url_param = [])
    {
        return [
            "add" => [
                "content" => '<em class="icon ni ni-plus"></em>  <span>Add</span>',
                "class" => "btn-primary",
                "href" => route($baseUrl . '.create', $url_param),
                "attrs" => ["title" => "Add new record"]
            ],
            // "pdf" => [
            //     "content" => '<em class="icon ni ni-file-pdf"></em>  <span>Pdf</span>',
            //     "class" => "btn-secondary",
            //     "href" => "",
            //     "attrs" => ["title" => "Export table content to PDF", 'url' => route($baseUrl . '.pdf', $url_param)]
            // ],
            "excel" => [
                "content" => '<em class="icon ni ni-file-xls"></em> <span>Excel</span>',
                "class" => "btn-excel",
                "href" => "",
                "attrs" => ["title" => "Export table content to Excel", 'url' => route($baseUrl . '.excel', $url_param)]
            ],
            // "csv" => [
            //     "content" => '<em class="icon ni ni-file-text"></em>  <span>CSV</span>',
            //     "class" => "btn-dark",
            //     "href" => "",
            //     "attrs" => ["title" => "Export table content to CSV", 'url' => route($baseUrl . '.csv', $url_param)]
            // ],
            "trash" => [
                "content" => '<em class="icon ni ni-trash-fill"></em>  <span>Trash</span>',
                "class" => "btn-danger",
                "href" => "",
                "attrs" => ["title" => "Open the Trash", 'url' => route($baseUrl . '.trash', $url_param)]
            ]
        ];
    }

    public static function default_action_config($baseUrl, $url_param = [])
    {
        $url_param['id'] = '@@id@@';
        return [
            "edit" => [
                "content" => '<em class="icon ni ni-edit"></em>',
                "class" => "btn-info",
                "href" => route($baseUrl . '.edit', $url_param),
                "attrs" => ["title" => "Edit this record",]
            ],
            "delete" => [
                "content" => '<em class="icon ni ni-trash eg-swal-av3 "></em>',
                "class" => "btn-danger text-white del-row",
                "href" => "",
                "attrs" => ["title" => "Remove this record", 'url' => route($baseUrl . '.delete', $url_param)]
            ]
        ];
    }

    public static function default_trash_config($baseUrl, $url_param)
    {
        $url_param['id'] = '@@id@@';
        return [
            "restore" => [
                "content" => '<em class="icon ni ni-reload"></em>',
                "class" => "btn-info",
                "href" => route($baseUrl . '.restore', $url_param),
                "attrs" => ["title" => "Restore this record",]
            ],
            "delete" => [
                "content" => '<em class="icon ni ni-trash eg-swal-av3 "></em>',
                "class" => "btn-danger text-white forcedel-row",
                "href" => "",
                "attrs" => ["title" => "Delete this record forever", 'url' => route($baseUrl . '.deleteforce', $url_param)]
            ]
        ];
    }

    public static function render_paging($baseurl, $totalcount, $page = 1, $perpage = 15, $maxdisplay = 5)
    {
        $pagelinks = array();
        $lastpage = 1;
        if ($totalcount < 1) return '<div class="pagination justify-content-center">NO ROW TO DISPLAY</div>';
        if ($totalcount > $perpage) $lastpage = ceil($totalcount / $perpage);
        else return '<div class="pagination justify-content-center">ALL IN ONE</div>';
        if ($page > $lastpage) $page = $lastpage;
        if ($page > round(($maxdisplay / 3) * 2)) {
            $currpage = $page - round($maxdisplay / 2);
            if ($currpage > ($lastpage - $maxdisplay)) {
                if (($lastpage - $maxdisplay) > 0) {
                    $currpage = $lastpage - $maxdisplay;
                }
            }
        } else {
            $currpage = 1;
        }
        $prevlink = '';
        $class = "btn page-link page-hover";
        if ($page > 1) {
            $prevlink = '<li><a class="' . $class . '" href="' . str_replace(':page', $page - 1, $baseurl) . '">Prev</a></li>';
        }
        $nextlink = '';
        if ($page < $lastpage) {
            $nextlink = '<li><a class="' . $class . '" href="' . str_replace(':page', $page + 1, $baseurl) . '">Next</a></li>';
        }
        $paging = '<div class="pagination justify-content-center">';
        $pagelinks[] = $prevlink;
        if ($currpage > 1) {
            $params['page'] = 1;
            $firstlink = '<li><a  class="' . $class . '" href="' . str_replace(':page', 1, $baseurl) . '">1</a></li>';

            $pagelinks[] = $firstlink;
            if ($currpage > 2) {
                $pagelinks[] = '<li><button  class="' . $class . '">...</button></li>';
            }
        }
        $displaycount = 0;
        while ($displaycount <= $maxdisplay and $currpage <= $lastpage) {
            if ($page == $currpage) {
                $pagelinks[] = '<li><button  class="' . $class . ' active">' . $currpage . '</button></li>';
            } else {
                $params['page'] = $currpage;
                $pagelink = '<li><a  class="' . $class . '" href="' . str_replace(':page', $currpage, $baseurl) . '">' . $currpage . '</a></li>';
                $pagelinks[] = $pagelink;
            }
            $displaycount++;
            $currpage++;
        }
        if ($currpage - 1 < $lastpage) {
            $params['page'] = $lastpage;
            $lastlink = '<li><a  class="' . $class . '" href="' . str_replace(':page', $lastpage, $baseurl) . '">' . $lastpage . '</a></li>';
            if ($currpage != $lastpage) {
                $pagelinks[] = '<li><button class="' . $class . '" href="#">...</button></li>';
            }
            $pagelinks[] = $lastlink;
        }
        $pagelinks[] = $nextlink;
        $paging .= implode(' ', $pagelinks);
        return $paging .= '</div>';
    }
}
