<?php

function _pagination_build_item(?int $page, string $url)
{
    if ($page == null) {
        return ['l' => '...', 'h' => null];
    }
    return ['l' => $page, 'h' => str_replace('%page%', $page, $url)];
}

function _pagination_render(array $items, int $currentPage)
{
    ?><ul class="pagination justify-content-center">
        <?php foreach ($items as $i) { ?>
            <li class="page-item <?= ($i['l'] == $currentPage ? 'active' : '') ?> ">
                <?php if ($i['l'] == $currentPage) { ?>
                    <span class="page-link">
                        <?= $currentPage ?>
                        <span class="sr-only"><?= _('(current)') ?></span>
                    </span>
                <?php } elseif (empty($i['h'])) { ?>
                    <span class="page-link"><?= $i['l'] ?></span>
                <?php } else { ?>
                    <a class="page-link" href="<?= $i['h'] ?>"><?= $i['l'] ?></a>
                <?php } ?>
            </li> 
        <?php } ?>
    </ul><?php
}

function pagination(string $url, int $pageCount, int $currentPage, int $adjacents = 2)
{
    $spans = [
        [1, 1 + $adjacents],
        [$currentPage - $adjacents, $currentPage + $adjacents],
        [$pageCount - $adjacents, $pageCount]
    ];

    // merge spans if they overlap or 'touch'
    $spans2 = [$spans[0]];
    $spans2idx = 0;
    for ($i = 1; $i < count($spans); $i++) {
        if ($spans2[$spans2idx][1] >= $spans[$i][0] - 1) {
            $spans2[$spans2idx][1] = $spans[$i][1];
        } else {
            $spans2[] = $spans[$i];
            $spans2idx++;
        }
    }

    // build items list
    $items = [];
    foreach ($spans2 as $span) {
        if ($spans2[0] != $span) {
            $items[] = _pagination_build_item(null, '');
        }
        for ($i = $span[0]; $i <= $span[1]; $i++) {
            $items[] = _pagination_build_item($i, $url);
        }
    }

    // render
    _pagination_render($items, $currentPage);
}