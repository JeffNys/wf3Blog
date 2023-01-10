<?php

namespace App\Service;

class PagingService
{
    public function pagination(int $totalPages, int $adjacents, int $page): array
    {
        $pivot = 0;
        $startFar = true;
        $endFar = true;
        $pages = [];

        if ($totalPages < 2 * $adjacents + 2) {
            // if there is not enought page for respect adjacents
            for ($i = 1; $i < $totalPages + 1; $i++) {
                $pages[] = $i;
            }
        } else {
            if ($page < $adjacents + 2) {
                // if we are not so far to start
                $pivot = $adjacents + 1;
                $startFar = false;
            } elseif ($page > $totalPages - ($adjacents + 1)) {
                // if we are not so far to end
                $pivot = $totalPages - $adjacents;
                $endFar = false;
            } else {
                $pivot = $page;
            }

            $pages[] = $pivot;
            for ($i = 1; $i < $adjacents - 1; $i++) {
                array_push($pages, $pivot + $i);
                array_unshift($pages, $pivot - $i);
            }
            if ($startFar) {
                array_unshift($pages, "...");
            } else {
                array_unshift($pages, 2);
            }

            if ($endFar) {
                array_push($pages, "...");
            } else {
                array_push($pages, $totalPages - 1);
            }

            array_push($pages, $totalPages);
            array_unshift($pages, 1);
        }

        return $pages;
    }

    /**
     * example with 10 pages and adjacents=2
     * 
     *  page = 1  => 1 2 3 ... 10
     *  page = 2  => 1 2 3 ... 10
     *  page = 3  => 1 2 3 ... 10
     *  page = 4  => 1 ... 4 ... 10
     *  ...
     *  page = 8  => 1 ... 8 9 10
     *  page = 9  => 1 ... 8 9 10
     *  page = 10  => 1 ... 8 9 10
     */

    /**
     * 4 possible cases
     * 1- there not enough pages
     * 2- page is not so far from start
     * 3- page is not so far to end
     * 4- far to start and end
     */



    /**
     * the view with bootstrap
     */

    //       <ul class="pagination">
    //       <li class="page-item {% if actualPage == 1 %}disabled{% endif %}">
    //           <a class="page-link" href="{{ path('app_article', {'page': actualPage - 1}) }}">&laquo;</a>
    //       </li>
    //       {% for page in pages %}
    //           <li class="page-item {% if actualPage == page %}active{% endif %} {% if '...' == page %}disable{% endif %}">
    //               {% if '...' == page %}
    //                   <span class="page-link">...</span>
    //               {% else %}
    //                   <a class="page-link" href="{{ path('app_article', {'page': page}) }}">{{ page }}</a>
    //               {% endif %}
    //           </li>
    //       {% endfor %}
    //       <li class="page-item {% if actualPage == lastPage %}disabled{% endif %}">
    //           <a class="page-link" href="{{ path('app_article', {'page': actualPage + 1}) }}">&raquo;</a>
    //       </li>
    //   </ul>

}
