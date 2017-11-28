<?php
        // custom pagination
    $link_limit = 7;
?>

@if ($paginator->lastPage() > 1)
    <?php
        $currentPage = $paginator->currentPage();
        $lastPage =     $paginator->lastPage();
    ?>
    <ul class="pagination">

        <li class="{{ ($currentPage == 1) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url(1) }}">«</a>
        </li>
        <li class="{{ ($currentPage == 1) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url($currentPage - 1) }}">‹</a>
        </li>

    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <?php
            $half_total_links = floor($link_limit / 2);
            $from = $currentPage - $half_total_links;
            $to = $currentPage + $half_total_links;
            if ($currentPage < $half_total_links) {
                $to += $half_total_links - $currentPage;
            }
            if ($lastPage - $currentPage < $half_total_links) {
                $from -= $half_total_links - ($lastPage - $currentPage) - 1;
            }
            ?>
            @if ($from < $i && $i < $to)
                <li class="{{ ($currentPage == $i) ? ' active' : '' }}">
                    <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endif
        @endfor
        <li class="{{ ($currentPage == $lastPage) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url($currentPage == $lastPage ? $lastPage: $currentPage + 1) }}">›</a>
        </li>

        <li class="{{ ($currentPage == $lastPage) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url($lastPage) }}">»</a>
        </li>
    </ul>
@endif