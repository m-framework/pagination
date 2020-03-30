<?php

namespace modules\pagination\client;

use libraries\helper\url;
use
    m\module,
    m\config,
    m\registry,
    m\view;

class pagination extends module {

    protected $css = ['/css/pagination.css'];

    public static $_name = '*Pagination*';

    public function __construct($count = null)
    {
        parent::__construct();

        if (!empty($count))
            return $this->pagination($count);
    }

    private function pagination($count)
    {
        if (empty($count) || !isset($this->view->pagination) || !isset($this->view->pagination_link)) {
//            view::set('pagination', '');
            return false;
        }

        $per_page = !empty($this->config->per_page) ? $this->config->per_page : 10;

        $page = registry::get('page_num');

        if (empty($page))
            $page = 1;

        $count_pages = ceil((int)$count / (int)$per_page);

        if ($count_pages == 1)
            return '';

        $limit_left = 4;
        $limit_right = 4;

        $pagination = '';

        if ($page > 1) {

            $back_page = url::to('/' . $this->clean_route . '/page/' . ($page - 1), null, true);

            registry::append('pagination_meta', ['href' => $back_page, 'rel' => 'prev']);

            $pagination .= $this->view->pagination_link->prepare([
                'link' => $back_page,
                'name' => 'Назад',
                'class' => ' class="move_page"'
            ]);

            if($page > $limit_left + 1) {

                $pagination .= $this->view->pagination_link->prepare([
                    'link' => url::to('/' . $this->clean_route . '/page/' . ($page - $limit_left - 1), null, true),
                    'name' => '...',
                    'class' => ''
                ]);
            }
        }

        $start = 1;
        $end = $count_pages;

        if ($page > $limit_left)
            $start = $page - $limit_left;

        if (($page + $limit_right) < $count_pages)
            $end = $page + $limit_right;

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page)
                $pagination .= $this->view->pagination_link->prepare([
                    'link' => '',
                    'name' => $i,
                    'class' => ' class="active"'
                ]);
            else
                $pagination .= $this->view->pagination_link->prepare([
                    'link' => url::to('/' . $this->clean_route . '/page/' . $i, null, true),
                    'name' => $i,
                    'class' => ' class="move_page"'
                ]);
        }

        if ($page < $count_pages) {

            if(($count_pages - $page) > $limit_right){

                $pagination .= $this->view->pagination_link->prepare([
                    'link' => url::to('/' . $this->clean_route . '/page/' . ($page + $limit_right + 1), null, true),
                    'name' => '...',
                    'class' => ''
                ]);
            }

            $next_page = url::to('/' . $this->clean_route . '/page/' . ($page + 1), null, true);

            registry::append('pagination_meta', ['href' => $next_page, 'rel' => 'next']);

            $pagination .= $this->view->pagination_link->prepare([
                'link' => $next_page,
                'name' => 'Вперед',
                'class' => ' class="move_page"'
            ]);
        }

        view::set('pagination', $this->view->pagination->prepare(['links' => $pagination]));
    }
}